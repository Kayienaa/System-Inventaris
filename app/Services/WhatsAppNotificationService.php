<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Susun teks pesan penagihan / pengingat peminjaman barang via WhatsApp.
     */
    public static function buildReminderMessage(Borrowing $borrowing): string
    {
        $borrower = $borrowing->borrower;
        $borrowerName = $borrower?->name ?? 'Peminjam';

        $identityParts = [];
        if ($borrower?->siswaProfile?->nis) {
            $identityParts[] = 'NIS: ' . $borrower->siswaProfile->nis;
        } elseif ($borrower?->guruProfile?->nip) {
            $identityParts[] = 'NIP: ' . $borrower->guruProfile->nip;
        }

        if ($borrower?->siswaProfile?->class_name) {
            $identityParts[] = 'Kelas: ' . $borrower->siswaProfile->class_name;
        }

        $identityStr = ! empty($identityParts) ? implode(', ', $identityParts) : '-';

        $asset = $borrowing->asset;
        $assetName = $asset?->name ?? 'Barang Inventaris';
        $assetCode = $asset?->asset_code ?? '-';
        $serialNumber = $asset?->serial_number ?: '-';

        $borrowedAt = $borrowing->borrowed_at
            ? $borrowing->borrowed_at->format('d/m/Y H:i') . ' WIB'
            : ($borrowing->requested_at ? $borrowing->requested_at->format('d/m/Y H:i') . ' WIB' : '-');

        $dueAt = $borrowing->due_at
            ? $borrowing->due_at->format('d/m/Y H:i') . ' WIB'
            : '-';

        $isOverdue = $borrowing->isOverdue() || ($borrowing->due_at && now()->isAfter($borrowing->due_at));
        $statusLabel = $isOverdue ? 'STATUS: SUDAH MELEWATI TENGGAT (OVERDUE) / SUDAH MELEWATI BATAS (OVERDUE)' : 'MENDEKATI TENGGAT';

        $lines = [];
        if ($isOverdue) {
            $lines[] = '*[PEMBERITAHUAN OVERDUE INVENTARIS TEFA SMKN 1 BANGSRI]*';
        } else {
            $lines[] = '*PENGINGAT PENGEMBALIAN BARANG INVENTARIS*';
        }
        $lines[] = '*TEFA SMKN 1 Bangsri*';
        $lines[] = '--------------------------------------------';
        $lines[] = "Halo *{$borrowerName}* ({$identityStr}),";
        $lines[] = '';
        $lines[] = 'Kami menginformasikan status peminjaman barang inventaris Anda:';
        $lines[] = '';
        $lines[] = '📦 *Detail Barang:*';
        $lines[] = "• Nama Barang: {$assetName}";
        $lines[] = "• Kode Unik: {$assetCode}";
        $lines[] = "• Serial Number: {$serialNumber}";
        $lines[] = '';
        $lines[] = '📅 *Waktu Peminjaman:*';
        $lines[] = "• Tanggal Pinjam: {$borrowedAt}";
        $lines[] = "• Batas Pengembalian (Due Date): {$dueAt}";
        $lines[] = '';
        $lines[] = "⚠️ *Status Peringatan:* *{$statusLabel}*";
        $lines[] = '';

        if ($isOverdue) {
            $lines[] = 'Masa peminjaman barang tersebut telah melewati batas waktu yang disepakati (OVERDUE). Mohon *SEGERA* mengembalikan unit barang beserta kelengkapannya ke ruang TEFA SMKN 1 Bangsri.';
        } else {
            $lines[] = 'Masa peminjaman barang tersebut sudah mendekati batas tenggat waktu. Mohon untuk mempersiapkan pengembalian unit tepat waktu.';
        }

        $lines[] = '';
        $lines[] = 'Jika barang sudah dikembalikan, silakan konfirmasi kepada petugas/laboran terkait.';
        $lines[] = 'Terima kasih atas perhatian dan kerja samanya.';
        $lines[] = '--------------------------------------------';
        $lines[] = 'Pesan resmi dari:';
        $lines[] = '*TEFA SMKN 1 Bangsri*';

        return implode("\n", $lines);
    }

    /**
     * Normalisasi nomor telepon ke format internasional Indonesia (murni angka: 628...).
     */
    public static function normalizePhoneNumber(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        // Bersihkan semua karakter selain angka
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);

        if (empty($digits)) {
            return '';
        }

        // 1. Jika diawali '620', ganti menjadi '62' (hapus '0' yang terjebak setelah 62)
        if (str_starts_with($digits, '620')) {
            return '62' . substr($digits, 3);
        }

        // 2. Jika diawali '62', biarkan tetap '62'
        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        // 3. Jika diawali '08', ubah menjadi '628'
        if (str_starts_with($digits, '08')) {
            return '628' . substr($digits, 2);
        }

        // 4. Jika diawali '0', ubah menjadi '62' diikuti sisa angkanya
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        // 5. Jika diawali '8', tambahkan '62' di depannya menjadi '628'
        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /**
     * Format nomor telepon untuk tampilan UI yang rapi (misal: +62 812-3456-7890).
     */
    public static function formatDisplayPhoneNumber(?string $phone): string
    {
        $normalized = self::normalizePhoneNumber($phone);
        if (empty($normalized)) {
            return '-';
        }

        // Format nomor seluler Indonesia: +62 8xx-xxxx-xxxx
        if (str_starts_with($normalized, '628')) {
            $rest = substr($normalized, 3);
            $len = strlen($rest);
            if ($len >= 7) {
                $part1 = substr($rest, 0, 2);
                $part2 = substr($rest, 2, 4);
                $part3 = substr($rest, 6);
                return "+62 8{$part1}-{$part2}-{$part3}";
            }
            return "+62 8{$rest}";
        }

        if (str_starts_with($normalized, '62')) {
            return '+62 ' . substr($normalized, 2);
        }

        return "+{$normalized}";
    }

    /**
     * Dapatkan URL direct chat WhatsApp dengan pesan penagihan yang telah di-encode.
     * Mengembalikan null jika peminjam belum memiliki nomor WhatsApp terdaftar.
     */
    public static function getWhatsAppUrl(Borrowing $borrowing): ?string
    {
        $user = $borrowing->borrower;

        // 1. Ambil nomor telepon peminjam dengan mengecek semua kemungkinan atribut dan relasi
        $rawPhone = $user?->siswaProfile?->phone 
            ?? $user?->siswaProfile?->no_hp 
            ?? $user?->siswaProfile?->nomor_hp 
            ?? $user?->guruProfile?->phone 
            ?? $user?->guruProfile?->no_hp 
            ?? $user?->phone 
            ?? $user?->no_hp;

        // 2. Normalisasi nomor tujuan secara ketat lewat normalizePhoneNumber()
        $phone = self::normalizePhoneNumber($rawPhone);

        // 3. Jika nomor di database kosong/null: jangan buat tautan fiktif / dummy, kembalikan null
        if (empty($phone)) {
            Log::warning("Peminjaman ID {$borrowing->id} oleh User ID {$user?->id} ({$user?->name}) tidak memiliki nomor WhatsApp terdaftar.");

            return null;
        }

        // Guard plausibilitas akhir: pastikan $phone yang sudah dinormalisasi cocok dengan regex nomor seluler Indonesia
        if (! preg_match('/^628[1-9][0-9]{6,10}$/', $phone)) {
            Log::warning("Peminjaman ID {$borrowing->id} oleh User ID {$user?->id} ({$user?->name}) memiliki nomor WhatsApp tidak valid / cacat / landline: {$phone}");

            return null;
        }

        // 4. Pastikan kembalian URL wajib berformat: https://wa.me/{nomor_clean}?text={encoded_message}
        $message = self::buildReminderMessage($borrowing);
        $encodedMessage = rawurlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }

    /**
     * Dapatkan nomor WhatsApp resmi pengirim Admin TEFA.
     * Memprioritaskan pengaturan dinamis dari database (system_settings: tefa_admin_whatsapp),
     * dan fallback ke konfigurasi services.whatsapp.admin_number / .env jika belum disetel.
     */
    public static function getAdminNumber(): string
    {
        $dynamicNumber = SystemSetting::get('tefa_admin_whatsapp');
        if (! empty($dynamicNumber)) {
            return self::normalizePhoneNumber((string) $dynamicNumber);
        }

        $configNumber = (string) config('services.whatsapp.admin_number', '6281234567890');

        return self::normalizePhoneNumber($configNumber);
    }

    /**
     * Alias untuk getAdminNumber().
     */
    public static function getAdminPhoneNumber(): string
    {
        return self::getAdminNumber();
    }

    /**
     * Dapatkan nomor WhatsApp resmi pengirim Admin TEFA dalam format tampilan rapi (+62 8xx...).
     */
    public static function getAdminDisplayPhoneNumber(): string
    {
        return self::formatDisplayPhoneNumber(self::getAdminNumber());
    }
}
