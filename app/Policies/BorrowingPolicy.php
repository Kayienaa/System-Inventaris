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

        if (! in_array($statusValue, [BorrowingStatus::Pending, BorrowingStatus::Delay], true)) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $this->owns($user, $borrowing);
    }

    public function checkout(User $user, Borrowing $borrowing): bool
    {
        $status = $borrowing->status instanceof BorrowingStatus 
            ? $borrowing->status 
            : BorrowingStatus::tryFrom((string) $borrowing->status);

        return $status === BorrowingStatus::Approved
            && ($user->id === $borrowing->borrower_user_id || $user->hasAnyRole(['admin', 'super_admin']));
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
