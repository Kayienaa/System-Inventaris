<?php

namespace App\Actions\Borrowings;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;
use Illuminate\Support\Facades\DB;

class CancelExpiredBorrowingsAction
{
    public function __construct(
        protected ?PromoteNextQueuedBorrowingAction $promote = null,
    ) {
        $this->promote = $this->promote ?? app(PromoteNextQueuedBorrowingAction::class);
    }

    /**
     * Cari dan hanguskan pengajuan peminjaman berstatus pending atau approved yang melewati batas waktu expires_at.
     */
    public function execute(): int
    {
        $expiredBorrowings = Borrowing::query()
            ->with(['asset', 'borrower'])
            ->whereIn('status', [BorrowingStatus::Pending, BorrowingStatus::Approved])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredBorrowings->isEmpty()) {
            return 0;
        }

        $processedCount = 0;

        foreach ($expiredBorrowings as $borrowing) {
            DB::transaction(function () use ($borrowing, &$processedCount): void {
                $lockedAsset = Asset::withTrashed()->lockForUpdate()->find($borrowing->asset_id);
                $locked      = Borrowing::query()->lockForUpdate()->find($borrowing->id);

                if ($locked === null || ! in_array($locked->status, [BorrowingStatus::Pending, BorrowingStatus::Approved], true)) {
                    return;
                }

                $locked->update([
                    'status' => BorrowingStatus::Rejected,
                    'rejection_reason' => 'Kedaluwarsa: Batas waktu serah terima terlewati.',
                    'rejected_at' => now(),
                    'due_at' => now(),
                ]);

                if ($lockedAsset) {
                    $this->promote->execute($lockedAsset);
                }

                \App\Models\AdminNotification::create([
                    'user_id' => null,
                    'type' => 'borrowing_expired',
                    'title' => 'Pemesanan Hangus Otomatis (10 Menit)',
                    'message' => "Peminjaman #{$locked->id} oleh {$borrowing->borrower?->name} untuk unit {$borrowing->asset?->name} hangus otomatis karena tidak serah terima dalam 10 menit.",
                    'data' => [
                        'borrowing_id' => $locked->id,
                        'asset_id' => $locked->asset_id,
                        'reason' => 'Batas waktu serah terima 10 menit kedaluwarsa.',
                        'time' => now()->toISOString(),
                    ],
                    'is_read' => false,
                ]);

                $processedCount++;
            });
        }

        return $processedCount;
    }
}
