<?php

namespace App\Console\Commands;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use Illuminate\Console\Command;

class ResetBorrowerPhonesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tefa:reset-borrower-phones {--force : Eksekusi reset tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan kolom nomor WhatsApp pada profil siswa dan guru (NULL) agar pengguna menginput nomor aslinya.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('Tindakan ini akan MENGHAPUS (NULL-kan) SEMUA nomor WhatsApp siswa & guru. Lanjutkan?')) {
                $this->warn('Operasi reset dibatalkan.');

                return Command::SUCCESS;
            }
        }

        $this->info('Memulai proses pembersihan nomor WhatsApp profil peminjam TEFA...');

        $siswaCount = SiswaProfile::whereNotNull('phone')->count();
        $guruCount = GuruProfile::whereNotNull('phone')->count();

        $this->line("Ditemukan: {$siswaCount} profil siswa dan {$guruCount} profil guru dengan nomor terisi.");

        // Reset semua nomor telepon siswa dan guru menjadi NULL
        $siswaUpdated = SiswaProfile::query()->update(['phone' => null]);
        $guruUpdated = GuruProfile::query()->update(['phone' => null]);

        $this->info("Berhasil mereset kolom nomor WhatsApp:");
        $this->line(" - Profil Siswa di-reset: {$siswaUpdated}");
        $this->line(" - Profil Guru di-reset : {$guruUpdated}");
        $this->info("Seluruh pengguna non-admin (Siswa & Guru) kini wajib melengkapi nomor WhatsApp aktif saat login berikutnya.");

        return Command::SUCCESS;
    }
}
