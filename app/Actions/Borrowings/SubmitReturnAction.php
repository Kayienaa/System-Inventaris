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

    public function execute(User $actor, Borrowing $borrowing, ?string $evidencePath = null, ?string $returnNote = null): Borrowing
    {
        $this->authorize($actor, 'submitReturn', $borrowing);

        return DB::transaction(function () use ($actor, $borrowing, $evidencePath, $returnNote): Borrowing {
            $locked = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($locked === null || $locked->status !== BorrowingStatus::Borrowed) {
                throw new BorrowingStateException('Only borrowed assets can be submitted for return.');
            }

            $locked->update([
                'status' => BorrowingStatus::ReturnPendingVerification,
                'return_submitted_at' => now(),
                'return_evidence_path' => $evidencePath ?: $locked->return_evidence_path,
                'return_note' => $returnNote,
            ]);

            $freshBorrowing = $locked->fresh();
            $freshBorrowing->loadMissing(['borrower', 'asset']);
            $borrower = $freshBorrowing->borrower ?? $actor;
            $asset = $freshBorrowing->asset;

            \App\Models\AdminNotification::create([
                'user_id' => null,
                'type' => 'return_submitted',
                'title' => 'Pengembalian Unit Masuk',
                'message' => "{$borrower->name} telah menyerahkan kembali unit {$asset?->name} ({$asset?->asset_code}).",
                'data' => [
                    'borrowing_id' => $freshBorrowing->id,
                    'borrower_name' => $borrower->name,
                    'asset_name' => $asset?->name ?? '-',
                    'asset_code' => $asset?->asset_code ?? '-',
                    'time' => now()->toISOString(),
                ],
                'is_read' => false,
            ]);

            return $freshBorrowing;
        });
    }
}
