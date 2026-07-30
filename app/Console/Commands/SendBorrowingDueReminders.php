<?php

namespace App\Console\Commands;

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
        $borrowings = Borrowing::with(['user', 'item'])
            ->active() // status = Dipinjam
            ->whereBetween('tgl_kembali_rencana', [now(), now()->addDay()])
            ->get();

        foreach ($borrowings as $borrowing) {
            if ($borrowing->user?->email) {
                Mail::to($borrowing->user->email)->queue(new DueReminderMail($borrowing));
            }
        }

        $this->info("Berhasil mengirim {$borrowings->count()} email pengingat.");

        return self::SUCCESS;
    }
}