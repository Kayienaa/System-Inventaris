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

    {{-- Welcome Card --}}
    <div class="welcome-card">
        <p class="welcome-small">
            {{ auth()->user()->hasRole('admin') ? 'Administrator Panel' : 'Panel Peminjam' }}
        </p>
        <h2 class="brand-font welcome-title">
            Selamat datang kembali, {{ auth()->user()->name }}.
        </h2>
        <p class="welcome-description">
            Pantau kondisi aset, ketersediaan barang, tren peminjaman mingguan,
            serta aktivitas inventaris yang membutuhkan perhatian dari satu tempat.
        </p>
    </div>

    {{-- Statistics Overview --}}
    <div class="section-heading">
        <h2>
            Ringkasan Inventaris
        </h2>
        <span>
            Status Real-time
        </span>
    </div>

    <div class="stats-grid">
        {{-- Total Aset --}}
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

        {{-- Barang Tersedia --}}
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p class="stat-label" style="margin-top:0; color: #059669;">
                        BARANG TERSEDIA
                    </p>
                </div>
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                    <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="stat-value" style="color: #059669;">
                {{ $barang_tersedia ?? ($status_aset['tersedia'] ?? 0) }}
            </p>
        </div>

        {{-- Barang Dipinjam --}}
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p class="stat-label" style="margin-top:0; color: var(--gold);">
                        SEDANG DIPINJAM
                    </p>
                </div>
                <div class="stat-icon" style="background: rgba(200, 155, 60, 0.12); color: var(--gold);">
                    <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                </div>
            </div>
            <p class="stat-value" style="color: var(--gold);">
                {{ $barang_dipinjam ?? ($status_aset['dipinjam'] ?? 0) }}
            </p>
        </div>

        {{-- Overdue --}}
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <p class="stat-label" style="margin-top:0; color: #dc2626;">
                        TERLAMBAT
                    </p>
                </div>
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                    <svg fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v5l3 1.75"/>
                    </svg>
                </div>
            </div>
            <p class="stat-value" style="color: #dc2626;">
                {{ $total_overdue ?? count($overdue) }}
            </p>
        </div>
    </div>

    {{-- =========================================
         VISUALISASI GRAFIK TREN PEMINJAMAN (CHART.JS)
    ========================================== --}}
    <div class="section-heading" style="margin-top: 2.25rem;">
        <h2>
            Visualisasi Tren Peminjaman
        </h2>
        <span>
            7 Hari Terakhir
        </span>
    </div>

    <div class="panel" style="margin-bottom: 2rem; padding: 1.5rem;">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="panel-title" style="margin-bottom: 0.25rem;">
                    Aktivitas Peminjaman Mingguan
                </h3>
                <p class="panel-subtitle" style="margin-bottom: 0;">
                    Jumlah transaksi peminjaman barang per hari selama 7 hari terakhir.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-[#6F4E37]"></span>
                Vintage Analytics
            </span>
        </div>

        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="borrowingTrendChart"></canvas>
        </div>
    </div>

    {{-- =========================================
         LEADERBOARDS ROW (ASET POPULER & PEMINJAM TERAKTIF)
    ========================================== --}}
    <div class="section-heading">
        <h2>
            Leaderboard &amp; Popularitas
        </h2>
        <span>
            Top Performa
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top 5 Aset Populer --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Top 5 Aset Paling Sering Dipinjam</h3>
                        <p class="text-xs text-gray-500">Aset dengan frekuensi transaksi tertinggi</p>
                    </div>
                </div>
            </div>

            @if(isset($popular_assets) && $popular_assets->count())
                <div class="space-y-3">
                    @foreach($popular_assets as $index => $asset)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 border border-gray-100 hover:bg-amber-50/30 transition">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $index === 0 ? 'bg-amber-400 text-white shadow-sm' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 text-gray-600')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $asset->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-mono text-gray-500">{{ $asset->asset_code }}</span>
                                        @if($asset->category)
                                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-100">{{ $asset->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#6F4E37]/10 text-[#6F4E37] border border-[#6F4E37]/20 shrink-0">
                                {{ $asset->borrowings_count }}x dipinjam
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada aktivitas peminjaman minggu ini.</p>
            @endif
        </div>

        {{-- Top 5 Peminjam Teraktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-xl bg-blue-50 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Top 5 Peminjam Teraktif</h3>
                        <p class="text-xs text-gray-500">Pengguna dengan transaksi peminjaman terbanyak</p>
                    </div>
                </div>
            </div>

            @if(isset($active_borrowers) && $active_borrowers->count())
                <div class="space-y-3">
                    @foreach($active_borrowers as $index => $user)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 border border-gray-100 hover:bg-blue-50/30 transition">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $index === 0 ? 'bg-blue-600 text-white shadow-sm' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 text-gray-600')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                                {{ $user->borrowings_count }} transaksi
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada aktivitas peminjaman minggu ini.</p>
            @endif
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
         LOWER PANELS (KATEGORI & OVERDUE)
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
                                <div class="category-bar-inner" style="width: {{ $percentage }}%;"></div>
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
                                    Jatuh tempo: {{ $entry['jatuh_tempo'] }}
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

    {{-- Script Chart.js untuk Diagram Batang Tren Peminjaman --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('borrowingTrendChart');
            if (!ctx) return;

            const labels = @json($chart_labels ?? []);
            const dataValues = @json($chart_data ?? []);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Transaksi Peminjaman',
                        data: dataValues,
                        backgroundColor: 'rgba(111, 78, 55, 0.85)',
                        hoverBackgroundColor: '#8B5A2B',
                        borderColor: '#6F4E37',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#2C1810',
                            titleColor: '#F5EBE6',
                            bodyColor: '#FFFFFF',
                            borderColor: '#8B5A2B',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y + ' transaksi peminjaman';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666666',
                                font: {
                                    family: "'DM Sans', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#666666',
                                font: {
                                    family: "'DM Sans', sans-serif",
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });
    </script>

@endsection
