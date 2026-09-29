<?php

namespace App\Console\Commands;

use App\Actions\Borrowings\CancelExpiredBorrowingsAction;
use Illuminate\Console\Command;

class CancelExpiredBorrowingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrowings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan otomatis transaksi peminjaman pending yang melewati batas waktu serah terima 10 menit';

    /**
     * Execute the console command.
     */
    public function handle(CancelExpiredBorrowingsAction $action): int
    {
        $count = $action->execute();

        if ($count > 0) {
            $this->info("Berhasil membatalkan {$count} pengajuan peminjaman kedaluwarsa (10 menit).");
        } else {
            $this->line('Tidak ada pengajuan peminjaman kedaluwarsa.');
        }

        return self::SUCCESS;
    }
}
