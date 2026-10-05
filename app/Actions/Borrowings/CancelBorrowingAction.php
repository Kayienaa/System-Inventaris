<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Exceptions\BorrowingStateException;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CancelBorrowingAction
{
    use AuthorizesBorrowingActions;

    public function __construct(
        protected ?PromoteNextQueuedBorrowingAction $promote = null,
    ) {
        $this->promote = $this->promote ?? app(PromoteNextQueuedBorrowingAction::class);
    }

    public function execute(User $actor, Borrowing $borrowing, ?string $reason = null): Borrowing
    {
        $this->authorize($actor, 'cancel', $borrowing);

        return DB::transaction(function () use ($actor, $borrowing, $reason): Borrowing {
            $asset = Asset::withTrashed()->lockForUpdate()->find($borrowing->asset_id);
            $lockedBorrowing = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($lockedBorrowing === null || ! in_array($lockedBorrowing->status, [BorrowingStatus::Pending, BorrowingStatus::Delay], true)) {
                throw new BorrowingStateException('Only pending or queued (delay) borrowings can be cancelled.');
            }

            // M3: Pastikan aset benar-benar dalam status Dipesan sebelum melepas kunci
            $holdsAsset = ($lockedBorrowing->status === BorrowingStatus::Pending)
                && $asset?->availability_status === AssetAvailabilityStatus::Dipesan;

            $reasonText = $reason ?: 'Dibatalkan oleh peminjam';

            // H1: Pembatalan oleh pengguna menggunakan status Cancelled (bukan Rejected)
            $lockedBorrowing->update([
                'status'               => BorrowingStatus::Cancelled,
                'cancelled_by_user_id' => $actor->id,
                'cancelled_at'         => now(),
                'cancellation_reason'  => $reasonText,
                'due_at'               => now(),
            ]);

            if ($holdsAsset && $asset) {
                $this->promote->execute($asset);
            }

            return $lockedBorrowing->fresh();
        });
    }
}
