@extends('layouts.app')

@section('title', 'Laporan | TE-Vault')

@section('content')

            {{-- Page heading --}}
            <div class="page-heading" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
                <div>
                    <h1 class="brand-font">
                        Laporan
                    </h1>
                    <p>
                        Ringkasan statistik dan rekapitulasi transaksi inventaris TEFA.
                    </p>
                </div>

                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                    <a
                        href="{{ route('admin.borrowings.export-excel') }}"
                        class="btn"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; background: #059669; color: #ffffff; font-size: 0.82rem; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ekspor Excel / CSV
                    </a>

                    <a
                        href="{{ route('admin.borrowings.export-pdf') }}"
                        target="_blank"
                        class="btn"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; background: #6F4E37; color: #ffffff; font-size: 0.82rem; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak / Ekspor PDF
                    </a>
                </div>
            </div>

            {{-- Stat ringkas --}}
            <div class="stats-grid">

                <div class="stat-card">
                    <p class="stat-label">Total Aset</p>
                    <p class="stat-value">{{ $total_aset }}</p>
                </div>

                <div class="stat-card">
                    <p class="stat-label">Kategori Terdaftar</p>
                    <p class="stat-value">{{ $per_kategori->count() }}</p>
                </div>

                <div class="stat-card">
                    <p class="stat-label">Peminjaman Terlambat</p>
                    <p class="stat-value">{{ $overdue->count() }}</p>
                </div>

            </div>

            <div class="content-grid">

                {{-- Distribusi Kategori --}}
                <div class="panel">

                    <h3 class="panel-title">
                        Distribusi Kategori
                    </h3>

                    <p class="panel-subtitle">
                        Jumlah aset berdasarkan kategori inventaris.
                    </p>

                    @if ($per_kategori->count())

                        @php
                            $maxKategori = max($per_kategori->max(), 1);
                        @endphp

                        <div class="category-list">

                            @foreach ($per_kategori as $nama => $total)

                                @php
                                    $percentage = ($total / $maxKategori) * 100;
                                @endphp

                                <div class="category-row">

                                    <span class="category-name">
                                        {{ $nama }}
                                    </span>

                                    <div class="category-bar">
                                        <div
                                            class="category-bar-inner"
                                            style="width: {{ $percentage }}%;"
                                        ></div>
                                    </div>

                                    <span class="category-count">
                                        {{ $total }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">
                            Belum ada data kategori.
                        </div>

                    @endif

                </div>

                {{-- Status Aset --}}
                <div class="panel">

                    <h3 class="panel-title">
                        Status Aset
                    </h3>

                    <p class="panel-subtitle">
                        Distribusi aset berdasarkan status ketersediaan.
                    </p>

                    @if ($status_aset->count())

                        <div class="category-list">

                            @foreach ($status_aset as $status => $total)

                                <div class="category-row">

                                    <span class="category-name">
                                        {{ ucfirst(str_replace('_', ' ', $status instanceof \BackedEnum ? $status->value : $status)) }}
                                    </span>

                                    <span class="category-count">
                                        {{ $total }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">
                            Belum ada data status aset.
                        </div>

                    @endif

                </div>

            </div>

            {{-- Overdue --}}
            <div class="panel" style="margin-top: 24px;">

                <h3 class="panel-title">
                    Peminjaman Terlambat
                </h3>

                <p class="panel-subtitle">
                    Barang yang belum dikembalikan melewati batas waktu.
                </p>

                @if ($overdue->count())

                    <table class="w-full text-left" style="margin-top: 16px;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--cream-dark);">
                                <th style="padding: 8px 0;">Peminjam</th>
                                <th style="padding: 8px 0;">Barang</th>
                                <th style="padding: 8px 0;">Jatuh Tempo</th>
                                <th style="padding: 8px 0;">Terlambat Sejak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdue as $entry)
                                <tr style="border-bottom: 1px solid var(--cream-dark);">
                                    <td style="padding: 8px 0;">{{ $entry['peminjam'] }}</td>
                                    <td style="padding: 8px 0;">{{ $entry['barang'] }}</td>
                                    <td style="padding: 8px 0;">{{ $entry['jatuh_tempo'] }}</td>
                                    <td style="padding: 8px 0; color: #c0392b;">{{ $entry['terlambat_sejak'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @else

                    <div class="empty-state">
                        Tidak ada peminjaman yang terlambat. ✦
                    </div>

                @endif

            </div>

@endsection
