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
        ?\DateTimeInterface $dueAt = null,
        string $purposeCategory = 'praktik',
        string $urgencyLevel = 'biasa'
    ): Borrowing {
        $this->authorize($borrower, 'create', Borrowing::class);

        return DB::transaction(function () use ($borrower, $asset, $borrowerNote, $borrowingEvidencePath, $dueAt, $purposeCategory, $urgencyLevel): Borrowing {
            $lockedAsset = Asset::withTrashed()->lockForUpdate()->find($asset->id);

            if (
                $lockedAsset === null
                || $lockedAsset->trashed()
                || in_array($lockedAsset->availability_status, [AssetAvailabilityStatus::Perbaikan, AssetAvailabilityStatus::TidakTersedia], true)
            ) {
                throw new AssetUnavailableException('The asset is not available for borrowing.');
            }

            // Jika user memiliki role guru dan keperluannya adalah mengajar, set is_teacher_priority = true
            $isTeacherPriority = $borrower->hasRole('guru') && $purposeCategory === 'mengajar';

            // Cek apakah aset sedang terantre / aktif dipesan / dipinjam
            $hasActiveBorrowing = Borrowing::query()
                ->where('asset_id', $lockedAsset->id)
                ->whereIn('status', [
                    BorrowingStatus::Pending,
                    BorrowingStatus::Approved,
                    BorrowingStatus::Borrowed,
                    BorrowingStatus::ReturnPendingVerification,
                ])
                ->exists();

            $isAvailableNow = ($lockedAsset->availability_status === AssetAvailabilityStatus::Tersedia) && ! $hasActiveBorrowing;
            $initialStatus = $isAvailableNow ? BorrowingStatus::Pending : BorrowingStatus::Delay;
            $expiresAt = ($initialStatus === BorrowingStatus::Pending) ? now()->addMinutes(10) : null;

            $requestedAt = now();
            $effectiveDueAt = $dueAt ?? $requestedAt->copy()->addDays(3);

            $borrowing = Borrowing::query()->create([
                'borrower_user_id' => $borrower->id,
                'asset_id' => $lockedAsset->id,
                'status' => $initialStatus,
                'urgency_level' => $urgencyLevel,
                'purpose_category' => $purposeCategory,
                'is_teacher_priority' => $isTeacherPriority,
                'requested_at' => $requestedAt,
                'borrowed_at' => null,
                'due_at' => $effectiveDueAt,
                'expires_at' => $expiresAt,
                'borrowing_evidence_path' => $borrowingEvidencePath,
                'borrower_note' => $borrowerNote,
            ]);

            // Kunci status ketersediaan aset menjadi 'dipesan' jika status pending
            if ($initialStatus === BorrowingStatus::Pending) {
                $lockedAsset->update([
                    'availability_status' => AssetAvailabilityStatus::Dipesan,
                ]);
            }

            $borrower->loadMissing(['siswaProfile', 'guruProfile']);
            $roleOrClass = $borrower->siswaProfile?->class_name 
                ?: ($borrower->guruProfile ? 'Guru' : (ucfirst($borrower->getRoleNames()->first() ?? 'Peminjam')));

            $priorityBadge = $isTeacherPriority ? ' [PRIORITAS GURU]' : ($urgencyLevel === 'mendesak' ? ' [MENDESAK]' : '');

            \App\Models\AdminNotification::create([
                'user_id' => null,
                'type' => 'borrow_requested',
                'title' => 'Peminjaman Baru Masuk' . $priorityBadge,
                'message' => "{$borrower->name} ({$roleOrClass}) mengajukan peminjaman unit {$lockedAsset->name} ({$lockedAsset->asset_code}). Status: " . ($initialStatus === BorrowingStatus::Pending ? 'Menunggu Serah Terima (10 Menit)' : 'Antrean (Delay)'),
                'data' => [
                    'borrowing_id' => $borrowing->id,
                    'borrower_name' => $borrower->name,
                    'role_or_class' => $roleOrClass,
                    'asset_name' => $lockedAsset->name,
                    'asset_code' => $lockedAsset->asset_code,
                    'urgency_level' => $urgencyLevel,
                    'purpose_category' => $purposeCategory,
                    'is_teacher_priority' => $isTeacherPriority,
                    'time' => now()->toISOString(),
                ],
                'is_read' => false,
            ]);

            return $borrowing;
        });
    }
}
