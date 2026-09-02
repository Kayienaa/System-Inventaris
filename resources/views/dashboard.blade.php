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


            {{-- =========================================
                 SIPINTU API GATEWAY & DATA SIJUNA SECTION
            ========================================== --}}
            @if (isset($sipintu_summary))
            <div class="section-heading" style="margin-top: 2.25rem;">
                <h2>
                    Integrasi SiPintu Gateway &amp; SIJUNA
                </h2>
                <span>
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ ($sipintu_summary['is_connected'] ?? false) ? '#10B981' : '#EF4444' }}; margin-right: 4px;"></span>
                    {{ ($sipintu_summary['is_connected'] ?? false) ? 'Gateway Terhubung (Online)' : 'Gateway Offline' }}
                </span>
            </div>

            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">

                {{-- Siswa / Pengguna SIJUNA --}}
                <a href="{{ route('sipintu.students.page') }}" class="stat-card" style="text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--gold)';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--cream-dark)';">
                    <div class="stat-top">
                        <div>
                            <p class="stat-label" style="margin-top:0; color: var(--gold); font-weight: 700;">
                                DATA PENGGUNA (SISWA)
                            </p>
                        </div>
                        <div class="stat-icon" style="background: rgba(200, 155, 60, 0.12); color: var(--gold);">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-value">
                        {{ number_format($sipintu_summary['total_students'] ?? 2306) }}
                    </p>
                    <span style="font-size: 0.78rem; color: var(--muted); font-weight: 500; display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--cream-dark);">
                        <span>Terdaftar di SIJUNA</span>
                        <span style="color: var(--brown); font-weight: 700;">Cek Data →</span>
                    </span>
                </a>

                {{-- Dewan Guru SIJUNA --}}
                <a href="{{ route('sipintu.teachers.page') }}" class="stat-card" style="text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--brown)';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--cream-dark)';">
                    <div class="stat-top">
                        <div>
                            <p class="stat-label" style="margin-top:0; color: var(--brown); font-weight: 700;">
                                DATA GURU
                            </p>
                        </div>
                        <div class="stat-icon" style="background: rgba(111, 78, 55, 0.12); color: var(--brown);">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-value">
                        {{ number_format($sipintu_summary['total_teachers'] ?? 71) }}
                    </p>
                    <span style="font-size: 0.78rem; color: var(--muted); font-weight: 500; display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--cream-dark);">
                        <span>Tenaga Pendidik SIJUNA</span>
                        <span style="color: var(--brown); font-weight: 700;">Cek Data →</span>
                    </span>
                </a>

                {{-- Status Gateway API --}}
                <a href="{{ route('sipintu.index') }}" class="stat-card" style="text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#10B981';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--cream-dark)';">
                    <div class="stat-top">
                        <div>
                            <p class="stat-label" style="margin-top:0; color: #059669; font-weight: 700;">
                                GATEWAY STATUS
                            </p>
                        </div>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                            <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418"/>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-value" style="font-size: 1.5rem; color: #059669;">
                        {{ ($sipintu_summary['is_connected'] ?? false) ? 'Terhubung' : 'Offline' }}
                    </p>
                    <span style="font-size: 0.78rem; color: var(--muted); font-weight: 500; display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--cream-dark);">
                        <span>{{ $sipintu_summary['total_requests'] ?? 0 }} API Requests</span>
                        <span style="color: var(--brown); font-weight: 700;">Monitoring →</span>
                    </span>
                </a>

            </div>
            @endif


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

                            @foreach ($overdue->take(5) as $entry)

                                <div class="overdue-item">

                                    <p class="overdue-name">
                                        {{ $entry['peminjam'] }}
                                    </p>

                                    <p class="overdue-item-name">
                                        {{ $entry['barang'] }}
                                    </p>

                                    <div class="overdue-meta">

                                        <span>
                                            Jatuh tempo:
                                            {{ $entry['jatuh_tempo'] }}
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
