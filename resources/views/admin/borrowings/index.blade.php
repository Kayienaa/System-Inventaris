@extends('layouts.app')

@section('title', 'Pusat Monitoring Peminjaman | TE-Vault')

@section('content')

<div
    class="max-w-7xl mx-auto px-4 sm:px-6 py-8"
    x-data="{
        selectedBorrowing: null,
        previewImage: null,
        openDetail(borrowing) {
            this.selectedBorrowing = borrowing;
        },
        closeDetail() {
            this.selectedBorrowing = null;
            this.previewImage = null;
        }
    }"
    @keydown.escape.window="closeDetail()"
>

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-[#6F4E37]/10 dark:bg-amber-950/40 flex items-center justify-center text-[#6F4E37] dark:text-neon-glowamber border border-[#6F4E37]/20 dark:border-amber-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-stone-800 dark:text-stone-100 tracking-tight">
                        Pusat Monitoring Peminjaman
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 dark:text-stone-400 mt-0.5">
                        Pengecekan, verifikasi serah terima, dan kontrol sirkulasi barang TEFA real-time
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('admin.borrowings.export-excel') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Excel
            </a>

            <a
                href="{{ route('admin.borrowings.export-pdf') }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] text-white text-xs font-semibold shadow-sm transition active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Ekspor PDF
            </a>
        </div>
    </div>

    {{-- Stats Overview Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-4 sm:p-5 transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Total Transaksi</span>
                <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-stone-800/80 flex items-center justify-center text-gray-600 dark:text-stone-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-stone-900 dark:text-stone-100 mt-2">{{ number_format($stats['total']) }}</p>
            <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Seluruh riwayat tercatat</p>
        </div>

        {{-- Sedang Dipinjam --}}
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-4 sm:p-5 transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-amber-800 dark:text-neon-glowamber uppercase tracking-wider">Sedang Dipinjam</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-neon-glowamber flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-stone-900 dark:text-stone-100 mt-2">{{ number_format($stats['borrowed']) }}</p>
            <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Unit berada di tangan user</p>
        </div>

        {{-- Selesai / Dikembalikan --}}
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-4 sm:p-5 transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-emerald-800 dark:text-neon-emerald uppercase tracking-wider">Dikembalikan</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-neon-emerald flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-stone-900 dark:text-stone-100 mt-2">{{ number_format($stats['returned']) }}</p>
            <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Selesai & kembali tersedia</p>
        </div>

        {{-- Terlambat / Overdue --}}
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-4 sm:p-5 transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-rose-800 dark:text-rose-400 uppercase tracking-wider">Overdue (Terlambat)</span>
                <span class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-stone-900 dark:text-stone-100 mt-2">{{ number_format($stats['overdue']) }}</p>
            <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Melewati batas waktu H+3</p>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="bg-white dark:bg-[#131B2A] border border-stone-200 dark:border-stone-800 rounded-2xl shadow-sm p-5 mb-6">
        <form action="{{ route('admin.borrowings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            {{-- Search Bar --}}
            <div class="sm:col-span-6">
                <label for="search" class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">
                    Pencarian Transaksi
                </label>
                <div class="relative">
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peminjam, NIS, NIP, kode barang (LP-TEFA-001)..."
                        class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-stone-50 dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 focus:border-amber-600 dark:focus:border-cyan-500 focus:ring-1 focus:ring-amber-600 dark:focus:ring-cyan-500 shadow-sm placeholder:text-stone-400"
                    >
                    <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="sm:col-span-3">
                <label for="status" class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">
                    Status Peminjaman
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-stone-50 dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 focus:border-amber-600 dark:focus:border-cyan-500 focus:ring-1 focus:ring-amber-600 dark:focus:ring-cyan-500 shadow-sm"
                >
                    <option value="">Semua Status</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam (Aktif)</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Selesai (Kembali)</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue (Terlambat)</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="sm:col-span-3 flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 py-2 px-4 rounded-xl bg-[#6F4E37] text-white text-xs font-bold hover:bg-[#5a3f2c] dark:bg-cyan-600 dark:hover:bg-cyan-500 dark:shadow-neon-cyan transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Terapkan
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a
                        href="{{ route('admin.borrowings.index') }}"
                        class="py-2 px-3 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-600 dark:text-stone-300 text-xs font-medium hover:bg-stone-100 dark:hover:bg-stone-700 transition flex items-center justify-center"
                        title="Reset Filter"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Monitoring Transaksi --}}
    <div class="bg-white dark:bg-[#131B2A] rounded-2xl shadow-sm border border-stone-200 dark:border-stone-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600 dark:text-stone-300">
                <thead class="bg-stone-50/80 dark:bg-[#0E1420] border-b border-stone-200 dark:border-stone-800 text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                    <tr>
                        <th class="px-5 py-4 w-12 text-center">No</th>
                        <th class="px-5 py-4">Peminjam</th>
                        <th class="px-5 py-4">Barang yang Dipinjam</th>
                        <th class="px-5 py-4">Waktu Pinjam</th>
                        <th class="px-5 py-4">Target Kembali</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                    @forelse($borrowings as $b)
                        @php
                            $isOverdue = $b->isOverdue();
                            $statusVal = $b->status->value ?? (string) $b->status;
                            $borrowerRole = $b->borrower?->roles->pluck('name')->first() ?? 'User';

                            $identityText = '-';
                            if ($b->borrower?->siswaProfile?->nis) {
                                $identityText = 'NIS: ' . $b->borrower->siswaProfile->nis;
                            } elseif ($b->borrower?->guruProfile?->nip) {
                                $identityText = 'NIP: ' . $b->borrower->guruProfile->nip;
                            }

                            $canSendWhatsApp = $isOverdue || $statusVal === 'borrowed';
                            $waUrl = $canSendWhatsApp ? \App\Services\WhatsAppNotificationService::getWhatsAppUrl($b) : null;

                            // Prepare structured payload for Alpine.js Detail modal
                            $detailPayload = [
                                'id' => $b->id,
                                'transaction_code' => '#TRX-' . str_pad((string) $b->id, 5, '0', STR_PAD_LEFT),
                                'borrower' => [
                                    'name' => $b->borrower?->name ?? 'Pengguna Dihapus',
                                    'email' => $b->borrower?->email ?? '-',
                                    'role' => ucfirst($borrowerRole),
                                    'identity' => $identityText,
                                    'phone' => $b->borrower?->siswaProfile?->phone ?? $b->borrower?->guruProfile?->phone ?? $b->borrower?->phone ?? '-',
                                    'formatted_phone' => \App\Services\WhatsAppNotificationService::formatDisplayPhoneNumber($b->borrower?->siswaProfile?->phone ?? $b->borrower?->guruProfile?->phone ?? $b->borrower?->phone),
                                    'class_name' => $b->borrower?->siswaProfile?->class_name ?? null,
                                ],
                                'asset' => [
                                    'id' => $b->asset?->id,
                                    'name' => $b->asset?->name ?? 'Aset Tidak Ditemukan',
                                    'asset_code' => $b->asset?->asset_code ?? '-',
                                    'brand' => $b->asset?->brand ?? '-',
                                    'model' => $b->asset?->model ?? '-',
                                    'serial_number' => $b->asset?->serial_number ?? '-',
                                    'category' => $b->asset?->category?->name ?? '-',
                                    'photo_url' => $b->asset?->photo_path ? asset('storage/' . $b->asset->photo_path) : null,
                                ],
                                'dates' => [
                                    'borrowed_at' => $b->borrowed_at ? $b->borrowed_at->format('d M Y, H:i') . ' WIB' : ($b->requested_at ? $b->requested_at->format('d M Y, H:i') . ' WIB' : '-'),
                                    'due_at' => $b->due_at ? $b->due_at->format('d M Y, H:i') . ' WIB' : '-',
                                    'returned_at' => $b->returned_at ? $b->returned_at->format('d M Y, H:i') . ' WIB' : null,
                                ],
                                'status' => $isOverdue ? 'overdue' : $statusVal,
                                'is_overdue' => $isOverdue,
                                'borrower_note' => $b->borrower_note ?: 'Tidak ada catatan',
                                'return_note' => $b->return_note ?: null,
                                'borrowing_evidence_url' => $b->borrowing_evidence_path ? asset('storage/' . $b->borrowing_evidence_path) : null,
                                'return_evidence_url' => $b->return_evidence_path ? asset('storage/' . $b->return_evidence_path) : null,
                                'wa_url' => $waUrl,
                            ];
                        @endphp
                        <tr class="border-b border-stone-100 dark:border-stone-800/80 hover:bg-stone-50/50 dark:hover:bg-cyan-500/5 transition-colors">
                            {{-- No --}}
                            <td class="px-5 py-4 text-center font-medium text-stone-400 dark:text-stone-500">
                                {{ $borrowings->firstItem() + $loop->index }}
                            </td>

                            {{-- Peminjam --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#6F4E37]/10 dark:bg-amber-950/50 text-[#6F4E37] dark:text-neon-glowamber font-bold text-xs flex items-center justify-center shrink-0 border border-[#6F4E37]/20 dark:border-amber-500/30">
                                        {{ strtoupper(substr($b->borrower?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-stone-800 dark:text-stone-100 text-xs">
                                            {{ $b->borrower?->name ?? 'User Tidak Diketahui' }}
                                        </p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="font-mono text-[11px] text-stone-500 dark:text-stone-400">
                                                {{ $identityText }}
                                            </span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold {{ $borrowerRole === 'guru' ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' : 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-neon-glowamber border border-amber-200 dark:border-amber-500/30' }}">
                                                {{ ucfirst($borrowerRole) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Barang yang Dipinjam --}}
                            <td class="px-5 py-4">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-stone-800 dark:text-stone-100 text-xs">
                                            {{ $b->asset?->asset_code ?? '-' }}
                                        </span>
                                        @if($b->asset?->category)
                                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 border border-stone-200 dark:border-stone-700">
                                                {{ $b->asset->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-stone-800 dark:text-stone-200 mt-0.5 font-medium">
                                        {{ $b->asset?->name ?? 'Barang Terhapus' }}
                                    </p>
                                    @if($b->asset?->serial_number)
                                        <p class="text-[10px] font-mono text-stone-400 dark:text-stone-500">
                                            SN: {{ $b->asset->serial_number }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- Waktu Pinjam --}}
                            <td class="px-5 py-4">
                                @if($b->borrowed_at)
                                    <p class="font-medium text-stone-800 dark:text-stone-200 text-xs">
                                        {{ $b->borrowed_at->format('d M Y') }}
                                    </p>
                                    <p class="text-[11px] text-stone-400 dark:text-stone-500 font-mono">
                                        {{ $b->borrowed_at->format('H:i') }} WIB
                                    </p>
                                @elseif($b->requested_at)
                                    <p class="font-medium text-stone-800 dark:text-stone-200 text-xs">
                                        {{ $b->requested_at->format('d M Y') }}
                                    </p>
                                    <p class="text-[11px] text-stone-400 dark:text-stone-500 font-mono">
                                        {{ $b->requested_at->format('H:i') }} WIB
                                    </p>
                                @else
                                    <span class="text-stone-400 dark:text-stone-500 italic">-</span>
                                @endif
                            </td>

                            {{-- Target Kembali --}}
                            <td class="px-5 py-4">
                                @if($b->due_at)
                                    <p class="font-semibold text-xs {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-stone-800 dark:text-stone-200' }}">
                                        {{ $b->due_at->format('d M Y') }}
                                    </p>
                                    <p class="text-[11px] font-mono {{ $isOverdue ? 'text-rose-500' : 'text-stone-400 dark:text-stone-500' }}">
                                        {{ $b->due_at->format('H:i') }} WIB
                                    </p>
                                    @if($isOverdue)
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Overdue
                                        </span>
                                    @endif
                                @else
                                    <span class="text-stone-400 dark:text-stone-500 italic">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 text-center">
                                @if($isOverdue)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30">
                                        ● Overdue
                                    </span>
                                @elseif($statusVal === 'returned')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-neon-emerald border border-emerald-300 dark:border-emerald-500/30">
                                        ✓ Selesai
                                    </span>
                                @elseif($statusVal === 'borrowed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-neon-glowamber border border-amber-300 dark:border-amber-500/30">
                                        ● Dipinjam
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-300 dark:border-stone-700">
                                        {{ ucfirst($statusVal) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($canSendWhatsApp && $waUrl)
                                        <a
                                            href="{{ $waUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 dark:hover:bg-emerald-600 text-emerald-600 dark:text-neon-emerald hover:text-white transition active:scale-95 border border-emerald-200 dark:border-emerald-500/30 hover:border-emerald-600 shadow-sm"
                                            title="Kirim Pengingat WhatsApp ke Peminjam"
                                        >
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    @elseif($canSendWhatsApp && !$waUrl)
                                        <button
                                            type="button"
                                            disabled
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-400 cursor-not-allowed border border-stone-200 dark:border-stone-700 shadow-sm opacity-60"
                                            title="Nomor WhatsApp peminjam belum terdaftar di profil"
                                        >
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        @click="openDetail({{ Js::from($detailPayload) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#6F4E37] dark:bg-stone-800 dark:hover:bg-cyan-600 text-stone-700 hover:text-white dark:text-stone-300 font-semibold text-xs transition active:scale-95 border border-stone-300 dark:border-stone-700 hover:border-[#6F4E37] dark:hover:border-cyan-600 shadow-sm cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-stone-400 dark:text-stone-500">
                                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-neon-glowamber flex items-center justify-center mx-auto mb-3 border border-amber-200 dark:border-amber-500/30">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-stone-700 dark:text-stone-200">Belum ada transaksi peminjaman</p>
                                <p class="text-xs text-stone-500 dark:text-stone-400 mt-1 max-w-sm mx-auto">
                                    Tidak ditemukan data transaksi yang sesuai dengan kriteria filter atau pencarian Anda.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($borrowings->hasPages())
            <div class="px-6 py-4 border-t border-stone-100 dark:border-stone-800 bg-stone-50/60 dark:bg-[#0E1420]/60">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL DETAIL LENGKAP TRANSAKSI PEMINJAMAN (ALPINE)
    ══════════════════════════════════════════════════ --}}
    <div
        x-show="selectedBorrowing !== null"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/65 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            class="max-w-2xl w-full mx-auto bg-white dark:bg-[#131B2A] rounded-2xl shadow-2xl border border-stone-200 dark:border-stone-800 overflow-hidden my-6"
            @click.away="closeDetail()"
        >
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-amber-50/90 via-orange-50/70 to-white dark:from-[#1E293B] dark:via-[#162032] dark:to-[#131B2A] px-6 py-4 border-b border-stone-200 dark:border-stone-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#6F4E37] text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-stone-800 dark:text-stone-100" x-text="'Transaksi ' + selectedBorrowing?.transaction_code"></h3>
                            <template x-if="selectedBorrowing?.is_overdue">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30">
                                    Overdue (Terlambat)
                                </span>
                            </template>
                            <template x-if="!selectedBorrowing?.is_overdue && selectedBorrowing?.status === 'returned'">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-neon-emerald border border-emerald-300 dark:border-emerald-500/30">
                                    ✓ Selesai
                                </span>
                            </template>
                            <template x-if="!selectedBorrowing?.is_overdue && selectedBorrowing?.status === 'borrowed'">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-neon-glowamber border border-amber-300 dark:border-amber-500/30">
                                    ● Dipinjam
                                </span>
                            </template>
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                            Rincian pengecekan data peminjam, aset fisik, tenggat & bukti serah terima
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="closeDetail()"
                    class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 p-1.5 rounded-lg hover:bg-stone-100 dark:hover:bg-stone-800 transition cursor-pointer"
                    title="Tutup Modal (Esc)"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                {{-- 1. Bagian Informasi Peminjam & Barang (Grid 2 Kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Bagian User --}}
                    <div class="bg-stone-50/70 dark:bg-[#0E1420] rounded-xl p-4 border border-stone-200/80 dark:border-stone-800">
                        <div class="flex items-center gap-2 mb-2.5 text-xs font-bold text-[#6F4E37] dark:text-neon-glowamber uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Peminjam
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <div>
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Nama Lengkap:</span>
                                <p class="font-bold text-stone-800 dark:text-stone-100 text-sm" x-text="selectedBorrowing?.borrower.name"></p>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Role Sistem:</span>
                                <span class="font-semibold px-2 py-0.5 rounded text-[11px] bg-white dark:bg-stone-800 text-stone-700 dark:text-stone-200 border border-stone-200 dark:border-stone-700" x-text="selectedBorrowing?.borrower.role"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Identitas (NIS/NIP):</span>
                                <span class="font-mono font-bold text-stone-800 dark:text-stone-200" x-text="selectedBorrowing?.borrower.identity"></span>
                            </div>
                            <template x-if="selectedBorrowing?.borrower.class_name">
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-400 dark:text-stone-500 text-[11px]">Kelas:</span>
                                    <span class="font-medium text-stone-700 dark:text-stone-300" x-text="selectedBorrowing?.borrower.class_name"></span>
                                </div>
                            </template>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Email:</span>
                                <span class="text-stone-600 dark:text-stone-400 truncate max-w-[150px]" x-text="selectedBorrowing?.borrower.email"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">No. Telepon / HP:</span>
                                <span class="font-mono text-stone-700 dark:text-stone-300" x-text="selectedBorrowing?.borrower.formatted_phone || selectedBorrowing?.borrower.phone || '-'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Barang --}}
                    <div class="bg-stone-50/70 dark:bg-[#0E1420] rounded-xl p-4 border border-stone-200/80 dark:border-stone-800">
                        <div class="flex items-center gap-2 mb-2.5 text-xs font-bold text-[#6F4E37] dark:text-neon-glowamber uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Barang Inventaris
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <div>
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Nama Aset:</span>
                                <p class="font-bold text-stone-800 dark:text-stone-100 text-sm" x-text="selectedBorrowing?.asset.name"></p>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Kode Aset:</span>
                                <span class="font-mono font-bold text-amber-900 dark:text-neon-glowamber bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-500/30 px-2 py-0.5 rounded text-[11px]" x-text="selectedBorrowing?.asset.asset_code"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Merk / Model:</span>
                                <span class="font-medium text-stone-700 dark:text-stone-300" x-text="(selectedBorrowing?.asset.brand || '-') + ' ' + (selectedBorrowing?.asset.model || '')"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Serial Number:</span>
                                <span class="font-mono text-stone-700 dark:text-stone-300" x-text="selectedBorrowing?.asset.serial_number"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">Kategori:</span>
                                <span class="font-semibold text-stone-600 dark:text-stone-300" x-text="selectedBorrowing?.asset.category"></span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 2. Bagian Waktu --}}
                <div class="bg-white dark:bg-[#131B2A] rounded-xl p-4 border border-stone-200 dark:border-stone-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-stone-700 dark:text-stone-300 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-[#6F4E37] dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Timeline Sirkulasi
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="bg-stone-50 dark:bg-[#0E1420] p-3 rounded-lg border border-stone-100 dark:border-stone-800">
                            <span class="text-[11px] text-stone-400 dark:text-stone-500 block mb-0.5">Waktu Pinjam</span>
                            <span class="font-bold text-stone-800 dark:text-stone-200" x-text="selectedBorrowing?.dates.borrowed_at"></span>
                        </div>

                        <div class="p-3 rounded-lg border" :class="selectedBorrowing?.is_overdue ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-500/30' : 'bg-amber-50/60 dark:bg-amber-950/40 border-amber-200 dark:border-amber-500/30'">
                            <span class="text-[11px] block mb-0.5" :class="selectedBorrowing?.is_overdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-amber-800 dark:text-neon-glowamber'">
                                Target Kembali (H+3)
                            </span>
                            <span class="font-bold" :class="selectedBorrowing?.is_overdue ? 'text-rose-700 dark:text-rose-300' : 'text-amber-900 dark:text-neon-glowamber'" x-text="selectedBorrowing?.dates.due_at"></span>
                        </div>

                        <div class="p-3 rounded-lg border" :class="selectedBorrowing?.dates.returned_at ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-500/30' : 'bg-stone-50 dark:bg-[#0E1420] border-stone-100 dark:border-stone-800'">
                            <span class="text-[11px] text-stone-400 dark:text-stone-500 block mb-0.5">Waktu Kembali Fisik</span>
                            <span
                                class="font-bold"
                                :class="selectedBorrowing?.dates.returned_at ? 'text-emerald-800 dark:text-neon-emerald' : 'text-stone-400 dark:text-stone-500 italic'"
                                x-text="selectedBorrowing?.dates.returned_at || 'Belum Dikembalikan'"
                            ></span>
                        </div>
                    </div>
                </div>

                {{-- 3. Bagian Catatan --}}
                <div class="bg-white dark:bg-[#131B2A] rounded-xl p-4 border border-stone-200 dark:border-stone-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-2 text-xs font-bold text-stone-700 dark:text-stone-300 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-[#6F4E37] dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Catatan Keperluan
                    </div>
                    <div class="bg-stone-50 dark:bg-[#0E1420] rounded-lg p-3 text-xs text-stone-700 dark:text-stone-300 border border-stone-100 dark:border-stone-800 leading-relaxed" x-text="selectedBorrowing?.borrower_note"></div>

                    <template x-if="selectedBorrowing?.return_note">
                        <div class="mt-3">
                            <span class="text-[11px] font-bold text-emerald-800 dark:text-neon-emerald uppercase tracking-wider block mb-1">Catatan Pengembalian:</span>
                            <div class="bg-emerald-50 dark:bg-emerald-950/40 rounded-lg p-3 text-xs text-emerald-900 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-500/30 leading-relaxed" x-text="selectedBorrowing?.return_note"></div>
                        </div>
                    </template>
                </div>

                {{-- 4. Bagian Bukti Foto Real-time --}}
                <div class="bg-white dark:bg-[#131B2A] rounded-xl p-4 border border-stone-200 dark:border-stone-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-stone-700 dark:text-stone-300 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-[#6F4E37] dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Bukti Foto Serah Terima Real-Time
                        </div>
                        <span class="text-[11px] text-stone-400 dark:text-stone-500">Webcam / Kamera Device</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Foto Bukti Saat Peminjaman --}}
                        <div>
                            <span class="text-[11px] font-semibold text-stone-600 dark:text-stone-400 block mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Bukti Saat Peminjaman
                            </span>

                            <template x-if="selectedBorrowing?.borrowing_evidence_url">
                                <div
                                    class="relative aspect-video rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 shadow-sm group cursor-pointer"
                                    @click="previewImage = selectedBorrowing?.borrowing_evidence_url"
                                >
                                    <img
                                        :src="selectedBorrowing?.borrowing_evidence_url"
                                        alt="Bukti Peminjaman"
                                        width="480"
                                        height="270"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                        </svg>
                                        Perbesar
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedBorrowing?.borrowing_evidence_url">
                                <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-stone-200 dark:border-stone-700 flex flex-col items-center justify-center text-stone-400 dark:text-stone-500 text-xs p-4 text-center">
                                    <svg class="w-6 h-6 text-stone-300 dark:text-stone-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Tidak ada foto serah terima peminjaman</span>
                                </div>
                            </template>
                        </div>

                        {{-- Foto Bukti Saat Pengembalian --}}
                        <div>
                            <span class="text-[11px] font-semibold text-stone-600 dark:text-stone-400 block mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :class="selectedBorrowing?.return_evidence_url ? 'bg-emerald-500' : 'bg-stone-300 dark:bg-stone-600'"></span>
                                Bukti Saat Pengembalian
                            </span>

                            <template x-if="selectedBorrowing?.return_evidence_url">
                                <div
                                    class="relative aspect-video rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 shadow-sm group cursor-pointer"
                                    @click="previewImage = selectedBorrowing?.return_evidence_url"
                                >
                                    <img
                                        :src="selectedBorrowing?.return_evidence_url"
                                        alt="Bukti Pengembalian"
                                        width="480"
                                        height="270"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                        </svg>
                                        Perbesar
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedBorrowing?.return_evidence_url">
                                <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-stone-200 dark:border-stone-700 flex flex-col items-center justify-center text-stone-400 dark:text-stone-500 text-xs p-4 text-center">
                                    <svg class="w-6 h-6 text-stone-300 dark:text-stone-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="selectedBorrowing?.dates.returned_at ? 'Tidak ada foto bukti pengembalian' : 'Barang belum dikembalikan'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="bg-stone-50 dark:bg-[#0E1420] px-6 py-3.5 border-t border-stone-200 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[11px] text-stone-400 dark:text-stone-500 font-mono" x-text="'ID Transaksi: #' + selectedBorrowing?.id"></span>
                <div class="flex items-center gap-2">
                    <template x-if="selectedBorrowing?.wa_url">
                        <a
                            :href="selectedBorrowing?.wa_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm active:scale-95"
                            title="Kirim Pengingat WhatsApp ke Peminjam"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Kirim Pengingat WhatsApp
                        </a>
                    </template>
                    <template x-if="!selectedBorrowing?.wa_url && (selectedBorrowing?.is_overdue || selectedBorrowing?.status === 'borrowed')">
                        <button
                            type="button"
                            disabled
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-400 dark:text-stone-500 text-xs font-semibold border border-stone-200 dark:border-stone-700 cursor-not-allowed opacity-75 shadow-sm"
                            title="Nomor WhatsApp peminjam belum terdaftar di profil"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Kirim Pengingat WhatsApp
                        </a>
                    </template>
                    <template x-if="!selectedBorrowing?.wa_url && (selectedBorrowing?.is_overdue || selectedBorrowing?.status === 'borrowed')">
                        <button
                            type="button"
                            disabled
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-400 dark:text-stone-500 text-xs font-semibold border border-stone-200 dark:border-stone-700 cursor-not-allowed opacity-75 shadow-sm"
                            title="Nomor WhatsApp peminjam belum terdaftar di profil"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Nomor WA Belum Terdaftar
                        </button>
                    </template>
                    <button
                        type="button"
                        @click="closeDetail()"
                        class="px-5 py-2 rounded-xl bg-[#6F4E37] text-white text-xs font-bold hover:bg-[#5a3f2c] transition shadow-sm cursor-pointer"
                    >
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox Zoom Foto Bukti --}}
    <div
        x-show="previewImage !== null"
        class="fixed inset-0 z-60 bg-black/90 flex items-center justify-center p-4"
        x-cloak
        @click="previewImage = null"
    >
        <div class="relative max-w-4xl max-h-[90vh]">
            <img :src="previewImage" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl border border-white/20" alt="Preview Foto Bukti">
            <button
                type="button"
                @click="previewImage = null"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 font-bold text-sm bg-white/20 px-3 py-1 rounded-lg backdrop-blur-md"
            >
                ✕ Tutup Gambar
            </button>
        </div>
    </div>

</div>

@endsection
