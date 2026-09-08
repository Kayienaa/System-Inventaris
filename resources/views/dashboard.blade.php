@extends('layouts.app')

@section('title', 'Dashboard | SITEFA')

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
            Minggu Berjalan
        </span>
    </div>

    <div x-data="{ showWeeklyHistoryModal: false }" class="panel" style="margin-bottom: 2rem; padding: 1.5rem;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h3 class="panel-title" style="margin-bottom: 0;">
                        Aktivitas Peminjaman Mingguan
                    </h3>
                    @if(isset($weekly_period_label) || isset($weeklyPeriodLabel))
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-stone-100 dark:bg-stone-800 text-[#6F4E37] dark:text-stone-300">
                            {{ $weekly_period_label ?? $weeklyPeriodLabel }}
                        </span>
                    @endif
                </div>
                <p class="panel-subtitle" style="margin-bottom: 0; margin-top: 0.25rem;">
                    Jumlah transaksi peminjaman barang per hari selama pekan ini (Senin s.d. Minggu).
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button 
                    type="button" 
                    @click="showWeeklyHistoryModal = true" 
                    class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-95 shadow-xs
                           bg-stone-100 hover:bg-[#6F4E37] text-stone-700 hover:text-white border border-stone-200/80 hover:border-[#6F4E37]
                           dark:bg-[#162032] dark:text-stone-300 dark:border-stone-700/80 dark:hover:border-amber-500/60 dark:hover:text-amber-300 dark:hover:shadow-[0_0_12px_rgba(245,158,11,0.25)] cursor-pointer"
                >
                    <!-- Ikon Jam/Riwayat dengan efek putar halus saat hover -->
                    <svg class="w-3.5 h-3.5 text-[#6F4E37] group-hover:text-white dark:text-amber-400 group-hover:rotate-12 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Lihat History Mingguan</span>
                </button>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-neon-glowamber border border-amber-200 dark:border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-[#6F4E37] dark:bg-neon-glowamber"></span>
                    Vintage Analytics
                </span>
            </div>
        </div>

        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="borrowingTrendChart"></canvas>
        </div>

        {{-- Modal Dialog Riwayat (History) Top Peminjaman Mingguan --}}
        <div
            x-show="showWeeklyHistoryModal"
            x-cloak
            @keydown.escape.window="showWeeklyHistoryModal = false"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            {{-- Backdrop --}}
            <div
                x-show="showWeeklyHistoryModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="showWeeklyHistoryModal = false"
                class="fixed inset-0 bg-stone-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
            ></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                <div
                    x-show="showWeeklyHistoryModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @click.stop
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#131B2A] border border-stone-200/90 dark:border-stone-800 text-left shadow-2xl transition-all w-full max-w-4xl max-h-[85vh] flex flex-col"
                >
                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-stone-200/80 dark:border-stone-800 flex items-center justify-between bg-stone-50/70 dark:bg-[#0B0F17]/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-[#6F4E37] dark:text-neon-glowamber border border-amber-200/60 dark:border-amber-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-stone-900 dark:text-stone-100">
                                    Riwayat Performa Peminjaman Mingguan
                                </h3>
                                <p class="text-xs text-stone-500 dark:text-stone-400">
                                    Rekapitulasi aktivitas transaksi, aset terpopuler, dan peminjam teraktif per pekan
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="showWeeklyHistoryModal = false"
                            class="p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 dark:hover:text-stone-200 dark:hover:bg-stone-800 transition-colors cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body with Custom Slim Scrollbar --}}
                    <div class="p-6 overflow-y-auto max-h-[70vh] pr-2 scroll-smooth [scrollbar-gutter:stable] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-stone-300 dark:[&::-webkit-scrollbar-thumb]:bg-stone-700 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-[#6F4E37] dark:hover:[&::-webkit-scrollbar-thumb]:bg-amber-500 transition-colors space-y-4 flex-1">
                        @if(isset($weekly_history) && count($weekly_history))
                            @foreach($weekly_history as $week)
                                <div class="bg-stone-50/80 dark:bg-[#0B0F17]/60 border {{ $week['is_current'] ? 'border-[#6F4E37]/50 dark:border-amber-500/40 ring-1 ring-[#6F4E37]/20 dark:ring-amber-500/20' : 'border-stone-200 dark:border-stone-800' }} rounded-2xl p-4 sm:p-5 transition hover:shadow-sm">
                                    {{-- Week Title & Stats --}}
                                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-stone-200/70 dark:border-stone-800 mb-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-stone-800 dark:text-stone-100">
                                                {{ $week['period'] }}
                                            </span>
                                            @if($week['is_current'])
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-300/80 dark:border-amber-700/50">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 dark:bg-amber-400 animate-pulse"></span>
                                                    Minggu Berjalan
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-[#6F4E37]/10 dark:bg-cyan-950/50 text-[#6F4E37] dark:text-neon-cyan border border-[#6F4E37]/20 dark:border-cyan-500/30">
                                            {{ $week['total_transactions'] }} Transaksi
                                        </span>
                                    </div>

                                    {{-- Grid Top 3 Aset & Top 3 Peminjam --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Top 3 Aset --}}
                                        <div>
                                            <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                                Top 3 Aset Dipinjam
                                            </h4>
                                            @if(count($week['top_assets']))
                                                <div class="space-y-2">
                                                    @foreach($week['top_assets'] as $idx => $asset)
                                                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-[#131B2A] border border-stone-200/60 dark:border-stone-800 text-xs">
                                                            <div class="flex items-center gap-2 truncate">
                                                                <span class="w-5 h-5 rounded flex items-center justify-center font-bold text-[10px] {{ $idx === 0 ? 'bg-amber-400 text-white' : 'bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300' }} shrink-0">
                                                                    #{{ $idx + 1 }}
                                                                </span>
                                                                <span class="font-medium text-stone-800 dark:text-stone-200 truncate" title="{{ $asset['name'] }}">
                                                                    {{ $asset['name'] }}
                                                                </span>
                                                            </div>
                                                            <span class="text-[11px] font-semibold text-[#6F4E37] dark:text-amber-300 shrink-0 ml-2">
                                                                {{ $asset['count'] }}x pinjam
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-xs text-stone-400 dark:text-stone-500 italic py-2">
                                                    Tidak ada peminjaman aset tercatat.
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Top 3 Peminjam --}}
                                        <div>
                                            <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-blue-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Top 3 Peminjam Teraktif
                                            </h4>
                                            @if(count($week['top_borrowers']))
                                                <div class="space-y-2">
                                                    @foreach($week['top_borrowers'] as $idx => $borrower)
                                                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-[#131B2A] border border-stone-200/60 dark:border-stone-800 text-xs">
                                                            <div class="flex items-center gap-2 truncate">
                                                                <span class="w-5 h-5 rounded flex items-center justify-center font-bold text-[10px] {{ $idx === 0 ? 'bg-blue-600 text-white' : 'bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300' }} shrink-0">
                                                                    #{{ $idx + 1 }}
                                                                </span>
                                                                <div class="truncate">
                                                                    <p class="font-medium text-stone-800 dark:text-stone-200 truncate">{{ $borrower['name'] }}</p>
                                                                    <span class="text-[10px] text-stone-400">{{ $borrower['identity'] }}</span>
                                                                </div>
                                                            </div>
                                                            <span class="text-[11px] font-semibold text-blue-700 dark:text-neon-cyan shrink-0 ml-2">
                                                                {{ $borrower['count'] }} transaksi
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-xs text-stone-400 dark:text-stone-500 italic py-2">
                                                    Tidak ada peminjam aktif tercatat.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-sm text-stone-500 dark:text-stone-400">
                                    Belum ada data riwayat mingguan yang tersedia.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-3.5 border-t border-stone-200/80 dark:border-stone-800 flex justify-end bg-stone-50/50 dark:bg-[#0B0F17]/30">
                        <button
                            type="button"
                            @click="showWeeklyHistoryModal = false"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 dark:bg-stone-800 dark:hover:bg-stone-700 dark:text-stone-200 border border-stone-200 dark:border-stone-700 transition-all active:scale-95 cursor-pointer"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
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
        <div class="bg-white border border-stone-200/80 shadow-sm rounded-2xl dark:bg-[#131B2A] dark:border-stone-800 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-stone-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-neon-glowamber border border-amber-200/40 dark:border-amber-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-stone-100 text-base">Top 5 Aset Paling Sering Dipinjam</h3>
                        <p class="text-xs text-gray-500 dark:text-stone-400">Aset dengan frekuensi transaksi tertinggi</p>
                    </div>
                </div>
            </div>

            @if(isset($popular_assets) && $popular_assets->count())
                <div class="space-y-3">
                    @foreach($popular_assets as $index => $asset)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 dark:bg-stone-900/60 border border-gray-100 dark:border-stone-800/80 hover:bg-amber-50/30 dark:hover:bg-cyan-500/10 transition">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $index === 0 ? 'bg-amber-400 text-white shadow-sm' : ($index === 1 ? 'bg-gray-300 dark:bg-stone-700 text-gray-700 dark:text-stone-200' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 dark:bg-stone-800 text-gray-600 dark:text-stone-400')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-stone-100 line-clamp-1">{{ $asset->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-mono text-gray-500 dark:text-stone-400">{{ $asset->asset_code }}</span>
                                        @if($asset->category)
                                            <span class="text-[10px] font-semibold text-amber-700 dark:text-neon-glowamber bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.2 rounded border border-amber-100 dark:border-amber-500/30">{{ $asset->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#6F4E37]/10 dark:bg-cyan-950/50 text-[#6F4E37] dark:text-neon-cyan border border-[#6F4E37]/20 dark:border-cyan-500/30 shrink-0">
                                {{ $asset->borrowings_count }}x dipinjam
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 dark:text-stone-500 py-6 text-center">Belum ada aktivitas peminjaman minggu ini.</p>
            @endif
        </div>

        {{-- Top 5 Peminjam Teraktif --}}
        <div class="bg-white border border-stone-200/80 shadow-sm rounded-2xl dark:bg-[#131B2A] dark:border-stone-800 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-stone-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-neon-cyan border border-blue-200/40 dark:border-cyan-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-stone-100 text-base">Top 5 Peminjam Teraktif</h3>
                        <p class="text-xs text-gray-500 dark:text-stone-400">Pengguna dengan transaksi peminjaman terbanyak</p>
                    </div>
                </div>
            </div>

            @if(isset($active_borrowers) && $active_borrowers->count())
                <div class="space-y-3">
                    @foreach($active_borrowers as $index => $user)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 dark:bg-stone-900/60 border border-gray-100 dark:border-stone-800/80 hover:bg-blue-50/30 dark:hover:bg-cyan-500/10 transition">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $index === 0 ? 'bg-blue-600 text-white shadow-sm' : ($index === 1 ? 'bg-gray-300 dark:bg-stone-700 text-gray-700 dark:text-stone-200' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 dark:bg-stone-800 text-gray-600 dark:text-stone-400')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-stone-100 line-clamp-1">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-stone-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-cyan-950/50 text-blue-700 dark:text-neon-cyan border border-blue-200 dark:border-cyan-500/30 shrink-0">
                                {{ $user->borrowings_count }} transaksi
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 dark:text-stone-500 py-6 text-center">Belum ada aktivitas peminjaman minggu ini.</p>
            @endif
        </div>
    </div>

    {{-- =========================================
         SIPINTU API GATEWAY & DATA SIJUNA SECTION
    ========================================== --}}
    @role('admin')
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
    @endrole

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

            function isDarkMode() {
                return document.documentElement.classList.contains('dark');
            }

            function getThemeColors() {
                const dark = isDarkMode();
                return {
                    barBg: dark ? '#F59E0B' : 'rgba(111, 78, 55, 0.85)',
                    barHoverBg: dark ? '#FBBF24' : '#8B5A2B',
                    barBorder: dark ? '#F59E0B' : '#6F4E37',
                    textColor: dark ? '#94A3B8' : '#666666',
                    gridColor: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)',
                    tooltipBg: dark ? '#0F172A' : '#2C1810',
                    tooltipTitle: dark ? '#F8FAFC' : '#F5EBE6',
                    tooltipBorder: dark ? '#06B6D4' : '#8B5A2B',
                };
            }

            const initialColors = getThemeColors();

            const chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Transaksi Peminjaman',
                        data: dataValues,
                        backgroundColor: initialColors.barBg,
                        hoverBackgroundColor: initialColors.barHoverBg,
                        borderColor: initialColors.barBorder,
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
                            backgroundColor: initialColors.tooltipBg,
                            titleColor: initialColors.tooltipTitle,
                            bodyColor: '#FFFFFF',
                            borderColor: initialColors.tooltipBorder,
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
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            ticks: {
                                stepSize: 5,
                                precision: 0,
                                color: initialColors.textColor,
                                font: {
                                    family: "'DM Sans', sans-serif",
                                    size: 11
                                },
                                callback: function(value) {
                                    return Number.isInteger(value) && value % 5 === 0 ? value : '';
                                }
                            },
                            grid: {
                                color: initialColors.gridColor,
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                color: initialColors.gridColor
                            },
                            ticks: {
                                color: initialColors.textColor,
                                font: {
                                    family: "'DM Sans', sans-serif",
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            window.addEventListener('theme-changed', function() {
                const colors = getThemeColors();
                chartInstance.data.datasets[0].backgroundColor = colors.barBg;
                chartInstance.data.datasets[0].hoverBackgroundColor = colors.barHoverBg;
                chartInstance.data.datasets[0].borderColor = colors.barBorder;
                chartInstance.options.scales.y.ticks.color = colors.textColor;
                chartInstance.options.scales.y.grid.color = colors.gridColor;
                chartInstance.options.scales.x.ticks.color = colors.textColor;
                chartInstance.options.scales.x.grid.color = colors.gridColor;
                if (chartInstance.options.plugins && chartInstance.options.plugins.tooltip) {
                    chartInstance.options.plugins.tooltip.backgroundColor = colors.tooltipBg;
                    chartInstance.options.plugins.tooltip.titleColor = colors.tooltipTitle;
                    chartInstance.options.plugins.tooltip.borderColor = colors.tooltipBorder;
                }
                chartInstance.update();
            });
        });
    </script>

@endsection
