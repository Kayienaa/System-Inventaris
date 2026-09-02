<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Peminjaman Aset | TE-VAULT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a1a;
            background: #f8fafc;
            padding: 24px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .no-print-bar {
            max-width: 1080px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: #6F4E37;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #5a3f2c;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-success {
            background: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background: #047857;
        }

        .report-page {
            max-width: 1080px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Kop Surat Formal */
        .kop-surat {
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 14px;
            margin-bottom: 24px;
            text-align: center;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .kop-unit {
            font-size: 15pt;
            font-weight: 800;
            color: #6F4E37;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 9.5pt;
            color: #4b5563;
        }

        .document-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .document-title h2 {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .document-title p {
            font-size: 9.5pt;
            color: #64748b;
            margin-top: 4px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 9.5pt;
            margin-bottom: 16px;
            color: #334155;
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 32px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.3px;
        }

        tr:nth-child(even) {
            background-color: #fcfdfe;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 8pt;
            text-transform: uppercase;
        }

        .badge-kembali {
            background: #dcfce7;
            color: #166534;
        }

        .badge-dipinjam {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-terlambat {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .signature-nip {
            font-size: 9pt;
            color: #4b5563;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 9pt;
            }

            .no-print-bar {
                display: none !important;
            }

            .report-page {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-kembali, .badge-dipinjam, .badge-terlambat {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- Action Bar (Hidden on Print) --}}
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('dashboard.analytics') }}" class="btn btn-secondary">
                ← Kembali ke Laporan
            </a>
            <span style="font-size: 13px; color: #64748b;">
                Rekapitulasi Transaksi Peminjaman ({{ $borrowings->count() }} Data)
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.borrowings.export-excel') }}" class="btn btn-success">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh File Excel / CSV
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Printable Report Sheet --}}
    <div class="report-page">
        
        {{-- Formal Kop Surat --}}
        <div class="kop-surat">
            <div class="kop-instansi">Pemerintah Provinsi Jawa Tengah &bull; Dinas Pendidikan dan Kebudayaan</div>
            <div class="kop-unit">SMK Negeri 1 Bangsri &bull; Teaching Factory (TEFA)</div>
            <div class="kop-alamat">Jl. Raya Bangsri - Krasak Km. 1, Bangsri, Kabupaten Jepara, Jawa Tengah 59453 &bull; Telp: (0291) 771234</div>
        </div>

        {{-- Document Header --}}
        <div class="document-title">
            <h2>Laporan Rekapitulasi Riwayat Peminjaman Aset</h2>
            <p>Sistem Manajemen Inventaris TE-VAULT SMKN 1 Bangsri</p>
        </div>

        {{-- Metadata Bar --}}
        <div class="meta-info">
            <div>
                <strong>Waktu Cetak:</strong> {{ $generatedAt->translatedFormat('l, d F Y - H:i:s') }} WIB
            </div>
            <div>
                <strong>Total Catatan:</strong> {{ $borrowings->count() }} Transaksi
            </div>
            <div>
                <strong>Dicetak Oleh:</strong> {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'Admin') }})
            </div>
        </div>

        {{-- Data Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th>Nama Peminjam</th>
                    <th>Identitas (NIS/NIP)</th>
                    <th>Kode / No Seri</th>
                    <th>Nama Aset</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Tgl Kembali</th>
                    <th style="width: 80px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowings as $index => $b)
                    @php
                        $identity = '-';
                        if ($b->borrower?->siswaProfile?->nis) {
                            $identity = 'NIS: ' . $b->borrower->siswaProfile->nis;
                        } elseif ($b->borrower?->guruProfile?->nip) {
                            $identity = 'NIP: ' . $b->borrower->guruProfile->nip;
                        } elseif ($b->borrower?->email) {
                            $identity = $b->borrower->email;
                        }

                        $isReturned = $b->status === \App\Enums\BorrowingStatus::Returned || $b->returned_at !== null;
                        $isOverdue = $b->due_at && $b->due_at < now() && in_array($b->status, [\App\Enums\BorrowingStatus::Borrowed, \App\Enums\BorrowingStatus::ReturnPendingVerification]);
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $b->borrower?->name ?? 'Pengguna' }}</strong>
                        </td>
                        <td style="font-family: monospace; font-size: 8.5pt;">{{ $identity }}</td>
                        <td style="font-family: monospace; font-size: 8.5pt;">
                            <div>{{ $b->asset?->asset_code ?? '-' }}</div>
                            <div style="color: #64748b; font-size: 8pt;">{{ $b->asset?->serial_number ?? '' }}</div>
                        </td>
                        <td>{{ $b->asset?->name ?? '-' }}</td>
                        <td>{{ $b->borrowed_at ? $b->borrowed_at->format('d/m/Y H:i') : ($b->requested_at ? $b->requested_at->format('d/m/Y H:i') : '-') }}</td>
                        <td>{{ $b->due_at ? $b->due_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $b->returned_at ? $b->returned_at->format('d/m/Y H:i') : '-' }}</td>
                        <td style="text-align: center;">
                            @if($isReturned)
                                <span class="status-badge badge-kembali">Kembali</span>
                            @elseif($isOverdue)
                                <span class="status-badge badge-terlambat">Terlambat</span>
                            @else
                                <span class="status-badge badge-dipinjam">Dipinjam</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8;">
                            Belum ada riwayat transaksi peminjaman aset.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Signatures Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala Lab / Pembimbing TEFA</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">Budi Santoso, S.Kom.</p>
                <p class="signature-nip">NIP. 19800101 200501 1 001</p>
            </div>

            <div class="signature-box">
                <p>Jepara, {{ $generatedAt->translatedFormat('d F Y') }}</p>
                <p><strong>Pengelola Inventaris TE-VAULT</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ auth()->user()->name }}</p>
                <p class="signature-nip">Petugas Administrasi Lab</p>
            </div>
        </div>

    </div>

</body>
</html>
