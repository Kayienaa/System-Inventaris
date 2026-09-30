<?php

namespace App\Actions\Borrowings;

use App\Enums\BorrowingStatus;
use App\Exceptions\BorrowingStateException;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RejectBorrowingAction
{
    use AuthorizesBorrowingActions;

    public function __construct(
        protected ?PromoteNextQueuedBorrowingAction $promote = null,
    ) {
        $this->promote = $this->promote ?? app(PromoteNextQueuedBorrowingAction::class);
    }

    public function execute(User $admin, Borrowing $borrowing, string $reason): Borrowing
    {
        $this->authorize($admin, 'reject', $borrowing);
        if (blank($reason)) {
            throw new InvalidArgumentException('A rejection reason is required.');
        }

        return DB::transaction(function () use ($admin, $borrowing, $reason): Borrowing {
            $asset = Asset::withTrashed()->lockForUpdate()->find($borrowing->asset_id);
            $lockedBorrowing = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($lockedBorrowing === null || ! in_array($lockedBorrowing->status, [BorrowingStatus::Pending, BorrowingStatus::Delay], true)) {
                throw new BorrowingStateException('Only pending or queued (delay) borrowings can be rejected.');
            }

            $holdsAsset = ($lockedBorrowing->status === BorrowingStatus::Pending);

            $lockedBorrowing->update([
                'status' => BorrowingStatus::Rejected,
                'rejected_by_user_id' => $admin->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'due_at' => now(),
            ]);

            if ($holdsAsset && $asset) {
                $this->promote->execute($asset);
            }

            return $lockedBorrowing->fresh();
        });
    }
}
