<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use Illuminate\Console\Command;

class FixBorrowerPhonesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tefa:fix-borrower-phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit dan normalisasi nomor WhatsApp peminjam di transaksi peminjaman (tanpa dummy)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memeriksa nomor WhatsApp peminjam pada transaksi peminjaman...');

        $borrowings = Borrowing::with(['borrower.siswaProfile', 'borrower.guruProfile'])->get();
        $normalizedCount = 0;
        $missingCount = 0;

        foreach ($borrowings as $borrowing) {
            $user = $borrowing->borrower;
            if (! $user) {
                continue;
            }

            // Cek apakah user sudah punya nomor telepon
            $currentPhone = $user->siswaProfile?->phone
                ?: $user->guruProfile?->phone
                ?: $user->phone;

            if (empty($currentPhone)) {
                $roleLabel = $user->siswaProfile ? 'Siswa (NIS: ' . ($user->siswaProfile->nis ?: '-') . ')' : ($user->guruProfile ? 'Guru (NIP: ' . ($user->guruProfile->nip ?: '-') . ')' : 'User');
                $this->warn("  [!] {$roleLabel} {$user->name} belum memiliki nomor WhatsApp terdaftar di profil.");
                $missingCount++;
            } else {
                // Normalisasi nomor telepon yang sudah ada
                $normalized = \App\Services\WhatsAppNotificationService::normalizePhoneNumber($currentPhone);
                if (! empty($normalized) && $normalized !== $currentPhone) {
                    if ($user->siswaProfile) {
                        $user->siswaProfile->update(['phone' => $normalized]);
                    } elseif ($user->guruProfile) {
                        $user->guruProfile->update(['phone' => $normalized]);
                    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                        $user->update(['phone' => $normalized]);
                    }
                    $this->line("  [✓] Normalisasi no HP {$user->name}: {$currentPhone} -> {$normalized}");
                    $normalizedCount++;
                }
            }
        }

        $this->info("Audit selesai. Dinormalisasi: {$normalizedCount}, Belum memiliki no HP: {$missingCount}.");

        return Command::SUCCESS;
    }
}
