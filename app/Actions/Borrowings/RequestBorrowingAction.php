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

            $requestedAt = now();
            $effectiveDueAt = $dueAt ?? $requestedAt->copy()->addDays(3);

            $borrowing = Borrowing::query()->create([
                'borrower_user_id' => $borrower->id,
                'asset_id' => $lockedAsset->id,
                'status' => BorrowingStatus::Pending,
                'requested_at' => $requestedAt,
                'borrowed_at' => null,
                'due_at' => $effectiveDueAt,
                'borrowing_evidence_path' => $borrowingEvidencePath,
                'borrower_note' => $borrowerNote,
            ]);

            $lockedAsset->update([
                'availability_status' => AssetAvailabilityStatus::Dipesan,
            ]);

            $borrower->loadMissing(['siswaProfile', 'guruProfile']);
            $roleOrClass = $borrower->siswaProfile?->class_name 
                ?: ($borrower->guruProfile ? 'Guru' : (ucfirst($borrower->getRoleNames()->first() ?? 'Peminjam')));

            \App\Models\AdminNotification::create([
                'user_id' => null,
                'type' => 'borrow_requested',
                'title' => 'Peminjaman Baru Masuk',
                'message' => "{$borrower->name} ({$roleOrClass}) mengajukan peminjaman unit {$lockedAsset->name} ({$lockedAsset->asset_code}).",
                'data' => [
                    'borrowing_id' => $borrowing->id,
                    'borrower_name' => $borrower->name,
                    'role_or_class' => $roleOrClass,
                    'asset_name' => $lockedAsset->name,
                    'asset_code' => $lockedAsset->asset_code,
                    'time' => now()->toISOString(),
                ],
                'is_read' => false,
            ]);

            return $borrowing;
        });
    }
}
