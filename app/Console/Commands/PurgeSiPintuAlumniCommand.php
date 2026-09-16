<?php

namespace App\Console\Commands;

use App\Services\SiPintuSyncService;
use Illuminate\Console\Command;

class PurgeSiPintuAlumniCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipintu:purge-alumni {--dry-run : Simulasi pembersihan tanpa menghapus data sebenarnya}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pembersihan akun alumni / siswa yang sudah lulus dari database lokal berdasarkan data SiPintu Gateway';

    /**
     * Execute the console command.
     */
    public function handle(SiPintuSyncService $syncService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('--- MODE SIMULASI (DRY-RUN) DIAKTIFKAN ---');
            $this->info('Memeriksa akun alumni dari SiPintu Gateway tanpa mengubah database lokal...');
        } else {
            $this->info('Memulai pembersihan akun alumni dari database lokal berdasarkan data SiPintu Gateway...');
        }

        $this->output->write('Memeriksa dan menyaring data alumni... ');
        $result = $syncService->purgeAlumni($dryRun);
        $this->output->writeln($result['success'] ? '<info>OK</info>' : '<error>FAIL</error>');

        if (! $result['success']) {
            $this->error('Gagal menjalankan pembersihan: ' . ($result['message'] ?? 'Unknown error'));

            return self::FAILURE;
        }

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Alumni Terdeteksi di SiPintu', $result['total_graduates']],
                ['Profil Siswa Dihapus', $result['purged_profiles']],
                ['Akun User Siswa Dihapus', $result['purged_users']],
                ['Dilewati (Peminjaman Aktif)', $result['skipped_active_borrowings']],
                ['Mode Operasi', $dryRun ? 'Simulasi (Dry-Run)' : 'Permanen'],
            ]
        );

        if ($result['skipped_active_borrowings'] > 0) {
            $this->warn("Terdapat {$result['skipped_active_borrowings']} akun alumni yang dipertahankan karena masih memiliki transaksi pinjam aktif.");
        }

        if ($dryRun) {
            $this->info('Simulasi selesai. Jalankan tanpa --dry-run untuk mengeksekusi penghapusan secara permanen.');
        } else {
            $this->info('Pembersihan akun alumni selesai. Database lokal kini hanya berisi warga SMK yang masih aktif.');
        }

        return self::SUCCESS;
    }
}
