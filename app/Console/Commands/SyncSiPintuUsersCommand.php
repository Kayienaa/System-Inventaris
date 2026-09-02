<?php

namespace App\Console\Commands;

use App\Services\SiPintuSyncService;
use Illuminate\Console\Command;

class SyncSiPintuUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipintu:sync-users {--type=all : students|teachers|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data pengguna (Siswa & Guru) dari SiPintu Gateway ke database lokal';

    /**
     * Execute the console command.
     */
    public function handle(SiPintuSyncService $syncService): int
    {
        $type = strtolower((string) $this->option('type'));

        $this->info('Memulai proses sinkronisasi pengguna dari SiPintu Gateway...');

        $rows = [];
        $hasFailure = false;

        if ($type === 'all' || $type === 'students') {
            $this->output->write('Menyinkronkan data Siswa... ');
            $studentResult = $syncService->syncStudents();
            $this->output->writeln($studentResult['success'] ? '<info>OK</info>' : '<error>FAIL</error>');

            $rows[] = [
                'Siswa',
                $studentResult['synced'],
                $studentResult['created'],
                $studentResult['updated'],
                $studentResult['errors'],
                $studentResult['success'] ? 'Sukses' : 'Gagal (' . $studentResult['message'] . ')',
            ];

            if (! $studentResult['success']) {
                $hasFailure = true;
            }
        }

        if ($type === 'all' || $type === 'teachers') {
            $this->output->write('Menyinkronkan data Guru... ');
            $teacherResult = $syncService->syncTeachers();
            $this->output->writeln($teacherResult['success'] ? '<info>OK</info>' : '<error>FAIL</error>');

            $rows[] = [
                'Guru',
                $teacherResult['synced'],
                $teacherResult['created'],
                $teacherResult['updated'],
                $teacherResult['errors'],
                $teacherResult['success'] ? 'Sukses' : 'Gagal (' . $teacherResult['message'] . ')',
            ];

            if (! $teacherResult['success']) {
                $hasFailure = true;
            }
        }

        $this->newLine();
        $this->table(
            ['Tipe Data', 'Total Diproses', 'User Baru', 'Diperbarui', 'Gagal/Error', 'Status'],
            $rows
        );

        if ($hasFailure) {
            $this->warn('Beberapa proses sinkronisasi mengalami kendala. Periksa log aplikasi untuk detail error.');

            return self::FAILURE;
        }

        $this->info('Seluruh data pengguna berhasil disinkronkan ke database lokal.');

        return self::SUCCESS;
    }
}
