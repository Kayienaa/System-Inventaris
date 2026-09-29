<?php

namespace App\Actions\Borrowings;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use App\Enums\BorrowingStatus;
use App\Exceptions\AssetUnavailableException;
use App\Exceptions\BorrowingStateException;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyReturnAction
{
    use AuthorizesBorrowingActions;

    public function execute(
        User $admin,
        Borrowing $borrowing,
        AssetCondition $returnCondition,
        ?string $verificationNote = null,
        ?string $damageEvidencePath = null
    ): Borrowing {
        $this->authorize($admin, 'verifyReturn', $borrowing);

        return DB::transaction(function () use ($admin, $borrowing, $returnCondition, $verificationNote, $damageEvidencePath): Borrowing {
            $asset = Asset::withTrashed()->lockForUpdate()->find($borrowing->asset_id);
            $lockedBorrowing = Borrowing::query()->lockForUpdate()->find($borrowing->id);

            if ($lockedBorrowing === null || $lockedBorrowing->status !== BorrowingStatus::ReturnPendingVerification) {
                throw new BorrowingStateException('Only submitted returns can be verified.');
            }
            if ($asset === null || $asset->trashed()) {
                throw new AssetUnavailableException('The asset is unavailable for return verification.');
            }

            $updateData = [
                'status' => BorrowingStatus::Returned,
                'returned_at' => now(),
                'return_condition' => $returnCondition,
                'return_verified_by_user_id' => $admin->id,
                'return_verified_at' => now(),
                'return_verification_note' => $verificationNote,
            ];

            if ($damageEvidencePath !== null) {
                $updateData['return_evidence_path'] = $damageEvidencePath;
            }

            $lockedBorrowing->update($updateData);

            $newAvailability = $returnCondition === AssetCondition::RusakBerat
                ? AssetAvailabilityStatus::Perbaikan
                : AssetAvailabilityStatus::Tersedia;

            $asset->update([
                'condition' => $returnCondition,
                'availability_status' => $newAvailability,
            ]);

            // Jika aset kembali tersedia, aktifkan antrean tertahan (delay) berikutnya jika ada
            if ($newAvailability === AssetAvailabilityStatus::Tersedia) {
                $nextQueue = Borrowing::query()
                    ->where('asset_id', $asset->id)
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
                    $asset->update(['availability_status' => AssetAvailabilityStatus::Dipesan]);
                }
            }

            return $lockedBorrowing->fresh();
        });
    }
}
