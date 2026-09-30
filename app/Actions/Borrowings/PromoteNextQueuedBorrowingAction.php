<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;

class PromoteNextQueuedBorrowingAction
{
    public function execute(Asset $asset): void
    {
        $next = Borrowing::query()
            ->where('asset_id', $asset->id)
            ->where('status', BorrowingStatus::Delay)
            ->orderByDesc('is_teacher_priority')
            ->orderByRaw("CASE WHEN urgency_level = 'mendesak' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->lockForUpdate()
            ->first();

        if ($next) {
            $next->update([
                'status' => BorrowingStatus::Pending,
                'expires_at' => now()->addMinutes(10),
            ]);
            $asset->update(['availability_status' => AssetAvailabilityStatus::Dipesan]);
        } else {
            $asset->update(['availability_status' => AssetAvailabilityStatus::Tersedia]);
        }
    }
}
