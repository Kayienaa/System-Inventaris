<?php

namespace App\Actions\Borrowings;

use App\Enums\BorrowingStatus;
use App\Exceptions\BorrowingStateException;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SubmitReturnAction
{
    use AuthorizesBorrowingActions;

    public function execute(User $actor, Borrowing $borrowing, string $evidencePath, ?string $returnNote = null): Borrowing
    {
        $this->authorize($actor, 'submitReturn', $borrowing);
        if (blank($evidencePath)) {
            throw new InvalidArgumentException('Return evidence is required.');
        }

        return DB::transaction(function () use ($borrowing, $evidencePath, $returnNote): Borrowing {
            $locked = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($locked === null || $locked->status !== BorrowingStatus::Borrowed) {
                throw new BorrowingStateException('Only borrowed assets can be submitted for return.');
            }

            $locked->update([
                'status' => BorrowingStatus::ReturnPendingVerification,
                'return_submitted_at' => now(),
                'return_evidence_path' => $evidencePath,
                'return_note' => $returnNote,
            ]);

            return $locked->fresh();
        });
    }
}
