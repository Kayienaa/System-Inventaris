<?php

namespace App\Console\Commands;

use App\Enums\BorrowingStatus;
use App\Mail\DueReminderMail;
use App\Models\Borrowing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBorrowingDueReminders extends Command
{
    protected $signature = 'borrowings:send-due-reminders';

    protected $description = 'Kirim email pengingat ke peminjam yang jatuh tempo dalam 24 jam ke depan';

    public function handle(): int
    {
        $borrowings = Borrowing::with(['borrower', 'asset', 'item'])
            ->where('status', BorrowingStatus::Borrowed)
            ->whereBetween('due_at', [now(), now()->addDay()])
            ->whereNull('due_reminder_sent_at')
            ->get();

        foreach ($borrowings as $borrowing) {
            if ($borrowing->borrower?->email) {
                Mail::to($borrowing->borrower->email)
                    ->queue(new DueReminderMail($borrowing));

                $borrowing->update([
                    'due_reminder_sent_at' => now(),
        ]);
    }
}

        $this->info("Berhasil mengirim {$borrowings->count()} email pengingat.");

        return self::SUCCESS;
    }
}