<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BorrowingReportController extends Controller
{
    /**
     * Ekspor data riwayat peminjaman ke format CSV / Excel.
     */
    public function exportCsv(): StreamedResponse
    {
        $borrowings = Borrowing::with(['borrower.siswaProfile', 'borrower.guruProfile', 'asset'])
            ->latest('id')
            ->get();

        $filename = 'laporan-peminjaman-tevault-' . Carbon::now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($borrowings) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($handle, [
                'No',
                'Nama Peminjam',
                'Identitas (NIS/NIP)',
                'Kode Barang',
                'Serial Number',
                'Nama Aset',
                'Tanggal Pinjam',
                'Batas Kembali',
                'Tanggal Kembali',
                'Status',
                'Catatan Peminjam',
            ]);

            $no = 1;
            foreach ($borrowings as $b) {
                // Tentukan Identitas (NIS/NIP)
                $identity = '-';
                if ($b->borrower?->siswaProfile?->nis) {
                    $identity = 'NIS: ' . $b->borrower->siswaProfile->nis;
                } elseif ($b->borrower?->guruProfile?->nip) {
                    $identity = 'NIP: ' . $b->borrower->guruProfile->nip;
                } elseif ($b->borrower?->email) {
                    $identity = $b->borrower->email;
                }

                // Tentukan Status Teks
                $statusText = 'Dipinjam';
                if ($b->status === BorrowingStatus::Returned || $b->returned_at !== null) {
                    $statusText = 'Kembali';
                } elseif ($b->due_at && $b->due_at < now() && in_array($b->status, [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])) {
                    $statusText = 'Terlambat';
                } elseif ($b->status === BorrowingStatus::Rejected) {
                    $statusText = 'Ditolak';
                } elseif ($b->status === BorrowingStatus::Cancelled) {
                    $statusText = 'Dibatalkan';
                }

                fputcsv($handle, [
                    $no++,
                    $b->borrower?->name ?? 'Pengguna',
                    $identity,
                    $b->asset?->asset_code ?? '-',
                    $b->asset?->serial_number ?? '-',
                    $b->asset?->name ?? '-',
                    $b->borrowed_at ? $b->borrowed_at->format('d/m/Y H:i') : ($b->requested_at ? $b->requested_at->format('d/m/Y H:i') : '-'),
                    $b->due_at ? $b->due_at->format('d/m/Y H:i') : '-',
                    $b->returned_at ? $b->returned_at->format('d/m/Y H:i') : '-',
                    $statusText,
                    $b->borrower_note ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tampilan Laporan Print-Friendly / Ekspor PDF.
     */
    public function exportPdf(Request $request)
    {
        $borrowings = Borrowing::with(['borrower.siswaProfile', 'borrower.guruProfile', 'asset.category'])
            ->latest('id')
            ->get();

        $generatedAt = Carbon::now();

        return view('admin.reports.borrowings-pdf', compact('borrowings', 'generatedAt'));
    }
}
