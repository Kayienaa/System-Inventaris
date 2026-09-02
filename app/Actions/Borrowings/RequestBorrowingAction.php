<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Exceptions\AssetUnavailableException;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\User;
use App\Support\Borrowings\BorrowingDueDateCalculator;
use Illuminate\Support\Facades\DB;

class RequestBorrowingAction
{
    use AuthorizesBorrowingActions;

    public function __construct(private readonly BorrowingDueDateCalculator $dueDates) {}

    public function execute(
        User $borrower,
        Asset $asset,
        ?string $borrowerNote = null,
        ?string $borrowingEvidencePath = null,
        ?\DateTimeInterface $dueAt = null
    ): Borrowing {
        $this->authorize($borrower, 'create', Borrowing::class);

        return DB::transaction(function () use ($borrower, $asset, $borrowerNote, $borrowingEvidencePath, $dueAt): Borrowing {
            $lockedAsset = Asset::withTrashed()->lockForUpdate()->find($asset->id);

            if ($lockedAsset === null || $lockedAsset->trashed() || $lockedAsset->availability_status !== AssetAvailabilityStatus::Tersedia) {
                throw new AssetUnavailableException('The asset is not available for borrowing.');
            }

            $borrowedAt = now();
            $effectiveDueAt = $dueAt ?? $borrowedAt->copy()->addDays(3);

            $borrowing = Borrowing::query()->create([
                'borrower_user_id' => $borrower->id,
                'asset_id' => $lockedAsset->id,
                'status' => BorrowingStatus::Borrowed,
                'requested_at' => $borrowedAt,
                'borrowed_at' => $borrowedAt,
                'due_at' => $effectiveDueAt,
                'borrowing_evidence_path' => $borrowingEvidencePath,
                'borrower_note' => $borrowerNote,
            ]);

            $lockedAsset->update([
                'availability_status' => AssetAvailabilityStatus::Dipinjam,
            ]);

            return $borrowing;
        });
    }
}
