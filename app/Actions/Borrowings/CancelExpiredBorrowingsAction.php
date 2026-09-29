<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;
use Illuminate\Support\Facades\DB;

class CancelExpiredBorrowingsAction
{
    /**
     * Cari dan hanguskan pengajuan peminjaman berstatus pending yang melewati batas waktu expires_at (10 menit).
     */
    public function execute(): int
    {
        $expiredBorrowings = Borrowing::query()
            ->with(['asset', 'borrower'])
            ->where('status', BorrowingStatus::Pending)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredBorrowings->isEmpty()) {
            return 0;
        }

        $processedCount = 0;

        foreach ($expiredBorrowings as $borrowing) {
            DB::transaction(function () use ($borrowing, &$processedCount): void {
                /** @var Borrowing|null $locked */
                $locked = Borrowing::query()->lockForUpdate()->find($borrowing->id);

                if ($locked === null || $locked->status !== BorrowingStatus::Pending) {
                    return;
                }

                $locked->update([
                    'status' => BorrowingStatus::Rejected,
                    'rejection_reason' => 'Pemesanan hangus otomatis: Peminjam tidak menemui Mas Donny di ruang TEFA dalam batas waktu 10 menit.',
                    'rejected_at' => now(),
                    'due_at' => now(),
                ]);

                $lockedAsset = Asset::query()->lockForUpdate()->find($locked->asset_id);

                if ($lockedAsset !== null) {
                    // Cek apakah ada antrean tertahan (delay) untuk aset ini
                    $nextQueue = Borrowing::query()
                        ->where('asset_id', $lockedAsset->id)
                        ->where('status', BorrowingStatus::Delay)
                        ->orderByDesc('is_teacher_priority')
                        ->orderByRaw("CASE WHEN urgency_level = 'mendesak' THEN 1 ELSE 2 END")
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if ($nextQueue !== null) {
                        $nextQueue->update([
                            'status' => BorrowingStatus::Pending,
                            'expires_at' => now()->addMinutes(10),
                        ]);
                        // Aset tetap Dipesan untuk peminjam antrean berikutnya
                        $lockedAsset->update(['availability_status' => AssetAvailabilityStatus::Dipesan]);
                    } else {
                        // Tidak ada antrean, kembalikan unit aset ke 'tersedia'
                        $lockedAsset->update(['availability_status' => AssetAvailabilityStatus::Tersedia]);
                    }
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
