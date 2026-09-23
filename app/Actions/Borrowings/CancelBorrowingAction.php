<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Exceptions\AssetUnavailableException;
use App\Exceptions\BorrowingStateException;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CancelBorrowingAction
{
    use AuthorizesBorrowingActions;

    public function execute(User $actor, Borrowing $borrowing, ?string $reason = null): Borrowing
    {
        $this->authorize($actor, 'cancel', $borrowing);

        return DB::transaction(function () use ($actor, $borrowing, $reason): Borrowing {
            $asset = Asset::withTrashed()->lockForUpdate()->find($borrowing->asset_id);
            $lockedBorrowing = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($lockedBorrowing === null || $lockedBorrowing->status !== BorrowingStatus::Pending) {
                throw new BorrowingStateException('Only pending borrowings can be cancelled.');
            }

            $reasonText = $reason ?: 'Dibatalkan oleh peminjam';

            $lockedBorrowing->update([
                'status' => BorrowingStatus::Rejected,
                'rejected_by_user_id' => $actor->id,
                'rejected_at' => now(),
                'rejection_reason' => $reasonText,
                'cancelled_by_user_id' => $actor->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $reasonText,
                'due_at' => now(),
            ]);

            $asset?->update(['availability_status' => AssetAvailabilityStatus::Tersedia]);

            return $lockedBorrowing->fresh();
        });
    }
}
