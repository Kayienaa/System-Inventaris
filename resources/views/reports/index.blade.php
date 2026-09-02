@extends('layouts.app')

@section('title', 'Laporan | TE-Vault')

@section('content')

            {{-- Page heading --}}
            <div class="page-heading">

                <h1 class="brand-font">
                    Laporan
                </h1>

                <p>
                    Ringkasan statistik inventaris TEFA.
                </p>

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
