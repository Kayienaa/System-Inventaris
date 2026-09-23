<?php

namespace App\Policies;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'guru', 'siswa']);
    }

    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $this->owns($user, $borrowing);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['guru', 'siswa']);
    }

    public function approve(User $user, Borrowing $borrowing): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function reject(User $user, Borrowing $borrowing): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function cancel(User $user, Borrowing $borrowing): bool
    {
        $statusValue = $borrowing->status instanceof BorrowingStatus
            ? $borrowing->status
            : BorrowingStatus::tryFrom((string) $borrowing->status);

        if ($statusValue !== BorrowingStatus::Pending) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $this->owns($user, $borrowing);
    }

    public function checkout(User $user, Borrowing $borrowing): bool
    {
        $statusValue = $borrowing->status instanceof BorrowingStatus
            ? $borrowing->status->value
            : (string) $borrowing->status;

        // Transaksi serah terima fisik hanya diizinkan jika sudah disetujui (approved)
        if ($statusValue !== 'approved') {
            return false;
        }

        // Admin & Super Admin diizinkan melakukan konfirmasi serah terima transaksi approved
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // Siswa dan Guru diizinkan jika transaksi milik mereka sendiri
        return $this->owns($user, $borrowing);
    }

    public function update(User $user, Borrowing $borrowing): bool
    {
        return $this->checkout($user, $borrowing);
    }

    public function submitReturn(User $user, Borrowing $borrowing): bool
    {
        $statusValue = $borrowing->status instanceof BorrowingStatus
            ? $borrowing->status->value
            : (string) $borrowing->status;

        if ($statusValue !== 'borrowed') {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        return $this->owns($user, $borrowing);
    }

    public function verifyReturn(User $user, Borrowing $borrowing): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    private function owns(User $user, Borrowing $borrowing): bool
    {
        $borrowerId = $borrowing->borrower_user_id ?? $borrowing->user_id ?? null;

        return $borrowerId !== null && (int) $borrowerId === (int) $user->id;
    }
}
