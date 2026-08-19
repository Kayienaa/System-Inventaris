@extends('layouts.app')

@section('title', 'Dashboard | TE-Vault')

@section('content')

            {{-- Page heading --}}
            <div class="page-heading">

                <h1 class="brand-font">
                    Dashboard
                </h1>

                <p>
                    Kelola dan pantau inventaris TEFA secara terpusat.
                </p>

            </div>


            {{-- Welcome --}}
            <div class="welcome-card">

                <p class="welcome-small">
                    Administrator Panel
                </p>

                <h2 class="brand-font welcome-title">
                    Selamat datang kembali, {{ auth()->user()->name }}.
                </h2>

                <p class="welcome-description">
                    Pantau kondisi aset, ketersediaan barang, kategori inventaris,
                    serta peminjaman yang membutuhkan perhatian dari satu tempat.
                </p>

            </div>


            {{-- Statistics --}}
            <div class="section-heading">

                <h2>
                    Ringkasan Inventaris
                </h2>

                <span>
                    Data saat ini
                </span>

            </div>


            <div class="stats-grid">

                {{-- Total aset --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>
                            <p class="stat-label" style="margin-top:0;">
                                TOTAL ASET
                            </p>
                        </div>

                        <div class="stat-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125V6c0-.621-.504-1.125-1.125-1.125H3.375C2.754 4.875 2.25 5.379 2.25 6v.375c0 .621.504 1.125 1.125 1.125z"/>
                            </svg>
                        </div>

                    </div>

                    <p class="stat-value">
                        {{ $total_aset }}
                    </p>

                </div>


                {{-- Kategori --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>
                            <p class="stat-label" style="margin-top:0;">
                                KATEGORI
                            </p>
                        </div>

                        <div class="stat-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3z"/>
                            </svg>
                        </div>

                    </div>

                    <p class="stat-value">
                        {{ count($per_kategori) }}
                    </p>

                </div>


                {{-- Status --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>
                            <p class="stat-label" style="margin-top:0;">
                                STATUS ASET
                            </p>
                        </div>

                        <div class="stat-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 3l8.25 4.5v6.75c0 4.5-3.75 7.5-8.25 8.25-4.5-.75-8.25-3.75-8.25-8.25V7.5L12 3z"/>
                            </svg>
                        </div>

                    </div>

                    <p class="stat-value">
                        {{ count($status_aset) }}
                    </p>

                </div>


                {{-- Overdue --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>
                            <p class="stat-label" style="margin-top:0;">
                                TERLAMBAT
                            </p>
                        </div>

                        <div class="stat-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 7.5v5l3 1.75"/>
                            </svg>
                        </div>

                    </div>

                    <p class="stat-value">
                        {{ count($overdue) }}
                    </p>

                </div>

            </div>


            {{-- =========================
                 LOWER PANELS
            ========================== --}}
            <div class="content-grid">


                {{-- Kategori --}}
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


                {{-- Overdue --}}
                <div class="panel">

                    <h3 class="panel-title">
                        Peminjaman Terlambat
                    </h3>

                    <p class="panel-subtitle">
                        Peminjaman yang melewati batas pengembalian.
                    </p>

                    @if ($overdue->count())

                        <div class="overdue-list">

                            @foreach ($overdue->take(5) as $item)

                                <div class="overdue-item">

                                    <p class="overdue-name">
                                        {{ $item['peminjam'] }}
                                    </p>

                                    <p class="overdue-item-name">
                                        {{ $item['barang'] }}
                                    </p>

                                    <div class="overdue-meta">

                                        <span>
                                            Jatuh tempo:
                                            {{ $item['jatuh_tempo'] }}
                                        </span>

                                        <span class="overdue-badge">
                                            Terlambat
                                        </span>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">
                            Tidak ada peminjaman yang terlambat. ✦
                        </div>

                    @endif

                </div>

            </div>


@endsection
