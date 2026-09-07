@extends('layouts.app')

@section('title', 'Pusat Monitoring Peminjaman | SITEFA')

@section('content')

<div
    class="max-w-7xl mx-auto px-4 sm:px-6 py-8"
    x-data="{
        selectedBorrowing: null,
        previewImage: null,
        rejectModalOpen: false,
        verifyModalOpen: false,
        rejectionReason: '',
        returnCondition: 'Baik',
        verificationNote: '',
        openDetail(borrowing) {
            this.selectedBorrowing = borrowing;
            this.rejectModalOpen = false;
            this.verifyModalOpen = false;
        },
        closeDetail() {
            this.selectedBorrowing = null;
            this.previewImage = null;
            this.rejectModalOpen = false;
            this.verifyModalOpen = false;
        }
    }"
    @keydown.escape.window="if (previewImage) { previewImage = null; } else { closeDetail(); }"
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
                        Pengecekan, verifikasi persetujuan & serah terima barang TEFA real-time
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

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-500/30 p-4 text-sm font-medium text-emerald-800 dark:text-neon-emerald flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 dark:text-neon-emerald shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Stats Overview Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-6">
        {{-- Total --}}
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider block truncate">Total Transaksi</span>
            <p class="text-2xl font-bold text-stone-900 dark:text-stone-100 mt-1.5">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>

        {{-- Pending --}}
        <div class="bg-white dark:bg-[#131B2A] border border-yellow-200/70 dark:border-yellow-900/50 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wider block truncate">Menunggu Persetujuan</span>
            <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-400 mt-1.5">{{ number_format($stats['pending'] ?? 0) }}</p>
        </div>

        {{-- Dipinjam --}}
        <div class="bg-white dark:bg-[#131B2A] border border-amber-200/70 dark:border-amber-900/50 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-semibold text-amber-800 dark:text-neon-glowamber uppercase tracking-wider block truncate">Sedang Dipinjam</span>
            <p class="text-2xl font-bold text-amber-800 dark:text-neon-glowamber mt-1.5">{{ number_format($stats['borrowed'] ?? 0) }}</p>
        </div>

        {{-- Return Pending --}}
        <div class="bg-white dark:bg-[#131B2A] border border-purple-200/70 dark:border-purple-900/50 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 uppercase tracking-wider block truncate">Menunggu Verifikasi</span>
            <p class="text-2xl font-bold text-purple-700 dark:text-purple-400 mt-1.5">{{ number_format($stats['return_pending'] ?? 0) }}</p>
        </div>

        {{-- Dikembalikan --}}
        <div class="bg-white dark:bg-[#131B2A] border border-emerald-200/70 dark:border-emerald-900/50 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-semibold text-emerald-800 dark:text-neon-emerald uppercase tracking-wider block truncate">Selesai Kembali</span>
            <p class="text-2xl font-bold text-emerald-800 dark:text-neon-emerald mt-1.5">{{ number_format($stats['returned'] ?? 0) }}</p>
        </div>

        {{-- Overdue --}}
        <div class="bg-white dark:bg-[#131B2A] border border-rose-200/70 dark:border-rose-900/50 rounded-2xl shadow-sm p-4 transition hover:shadow-md">
            <span class="text-[11px] font-semibold text-rose-800 dark:text-rose-400 uppercase tracking-wider block truncate">Overdue</span>
            <p class="text-2xl font-bold text-rose-800 dark:text-rose-400 mt-1.5">{{ number_format($stats['overdue'] ?? 0) }}</p>
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
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Siap Ambil)</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam (Aktif)</option>
                    <option value="return_pending_verification" {{ request('status') === 'return_pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi Pengembalian</option>
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
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                    @forelse ($borrowings as $b)
                        @php
                            $statusVal = $b->status->value ?? (string) $b->status;
                            $isOverdue = $b->isOverdue();
                            $borrowerRole = $b->borrower?->roles->pluck('name')->first() ?? 'User';
                            $identityText = $b->borrower?->siswaProfile?->nis ? 'NIS: ' . $b->borrower->siswaProfile->nis : ($b->borrower?->guruProfile?->nip ? 'NIP: ' . $b->borrower->guruProfile->nip : '-');
                            $canSendWhatsApp = $isOverdue || $statusVal === 'borrowed';
                            $waUrl = $canSendWhatsApp ? \App\Services\WhatsAppNotificationService::getWhatsAppUrl($b) : null;

                            $detailPayload = [
                                'id' => $b->id,
                                'transaction_code' => '#TRX-' . str_pad((string) $b->id, 5, '0', STR_PAD_LEFT),
                                'borrower' => [
                                    'name' => $b->borrower?->name ?? 'User',
                                    'email' => $b->borrower?->email ?? '-',
                                    'role' => ucfirst($borrowerRole),
                                    'identity' => $identityText,
                                    'phone' => $b->borrower?->siswaProfile?->phone ?? $b->borrower?->guruProfile?->phone ?? $b->borrower?->phone ?? '-',
                                    'formatted_phone' => \App\Services\WhatsAppNotificationService::formatDisplayPhoneNumber($b->borrower?->siswaProfile?->phone ?? $b->borrower?->guruProfile?->phone ?? $b->borrower?->phone),
                                    'class_name' => $b->borrower?->siswaProfile?->class_name ?? null,
                                ],
                                'asset' => [
                                    'id' => $b->asset?->id,
                                    'name' => $b->asset?->name ?? '-',
                                    'asset_code' => $b->asset?->asset_code ?? '-',
                                    'brand' => $b->asset?->brand ?? '-',
                                    'model' => $b->asset?->model ?? '-',
                                    'serial_number' => $b->asset?->serial_number ?? '-',
                                    'category' => $b->asset?->category?->name ?? '-',
                                    'photo_url' => $b->asset?->photo_url,
                                ],
                                'dates' => [
                                    'requested_at' => $b->requested_at ? $b->requested_at->format('d M Y, H:i') . ' WIB' : '-',
                                    'borrowed_at' => $b->borrowed_at ? $b->borrowed_at->format('d M Y, H:i') . ' WIB' : ($b->requested_at ? $b->requested_at->format('d M Y, H:i') . ' WIB' : '-'),
                                    'due_at' => $b->due_at ? $b->due_at->format('d M Y, H:i') . ' WIB' : '-',
                                    'returned_at' => $b->returned_at ? $b->returned_at->format('d M Y, H:i') . ' WIB' : null,
                                ],
                                'status' => $isOverdue ? 'overdue' : $statusVal,
                                'raw_status' => $statusVal,
                                'is_overdue' => $isOverdue,
                                'borrower_note' => $b->borrower_note ?: 'Tidak ada catatan',
                                'return_note' => $b->return_note ?: null,
                                'borrowing_evidence_url' => $b->borrowing_evidence_path ? asset('storage/' . $b->borrowing_evidence_path) : null,
                                'return_evidence_url' => $b->return_evidence_path ? asset('storage/' . $b->return_evidence_path) : null,
                                'approved_by' => $b->approvedBy?->name,
                                'return_verified_by' => $b->returnVerifiedBy?->name,
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
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 overflow-hidden shrink-0 flex items-center justify-center aspect-square">
                                        @if ($b->asset?->photo_url)
                                            <img
                                                src="{{ $b->asset->photo_url }}"
                                                alt="{{ $b->asset->name }}"
                                                width="40"
                                                height="40"
                                                loading="lazy"
                                                decoding="async"
                                                onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                class="w-full h-full object-cover aspect-square"
                                            >
                                            <div class="hidden w-full h-full bg-stone-900/60 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-stone-900/60 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
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
                                    </div>
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
                                        {{ $b->requested_at->format('H:i') }} WIB <span class="text-[10px] text-amber-600 font-sans">(Diajukan)</span>
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
                                @elseif($statusVal === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-yellow-50 text-yellow-700 dark:bg-yellow-950/50 dark:text-yellow-400 border border-yellow-300 dark:border-yellow-500/30">
                                        ● Menunggu Persetujuan
                                    </span>
                                @elseif($statusVal === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-neon-cyan border border-blue-300 dark:border-cyan-500/30">
                                        ✓ Disetujui
                                    </span>
                                @elseif($statusVal === 'borrowed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-neon-glowamber border border-amber-300 dark:border-amber-500/30">
                                        ● Dipinjam
                                    </span>
                                @elseif($statusVal === 'return_pending_verification')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30">
                                        ● Menunggu Verifikasi
                                    </span>
                                @elseif($statusVal === 'returned')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-neon-emerald border border-emerald-300 dark:border-emerald-500/30">
                                        ✓ Selesai
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
                                    @if($canSendWhatsApp)
                                        @if($waUrl)
                                            <a
                                                href="{{ $waUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 dark:hover:bg-emerald-600 text-emerald-600 dark:text-neon-emerald hover:text-white transition active:scale-95 border border-emerald-200 dark:border-emerald-500/30 shadow-sm"
                                                title="Kirim Pengingat WhatsApp ke Peminjam"
                                            >
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </a>
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-400 dark:text-stone-600 border border-stone-200 dark:border-stone-700 cursor-not-allowed opacity-60"
                                                title="Nomor WhatsApp peminjam belum terdaftar di profil"
                                            >
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    <button
                                        type="button"
                                        @click="openDetail({{ Js::from($detailPayload) }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#6F4E37] dark:bg-stone-800 dark:hover:bg-cyan-600 text-stone-700 hover:text-white dark:text-stone-300 font-semibold text-xs transition active:scale-95 border border-stone-300 dark:border-stone-700 shadow-sm cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Kelola
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-stone-400 dark:text-stone-500">
                                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-neon-glowamber flex items-center justify-center mx-auto mb-3 border border-amber-200 dark:border-amber-500/30">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-stone-700 dark:text-stone-300">Tidak Ada Transaksi</h3>
                                <p class="text-xs text-stone-400 dark:text-stone-500 mt-1">Belum ada data peminjaman yang cocok dengan filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-800">
            {{ $borrowings->links() }}
        </div>
    </div>

    {{-- Modal Detail Transaksi & Kelola Aksi --}}
    <div
        x-show="selectedBorrowing !== null"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div
            class="max-w-3xl w-full mx-auto rounded-2xl bg-white dark:bg-[#131B2A] shadow-2xl border border-stone-200 dark:border-stone-800 overflow-hidden"
            @click.away="closeDetail()"
        >
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-stone-50 to-stone-100 dark:from-stone-900 dark:to-[#131B2A] px-6 py-4 border-b border-stone-200 dark:border-stone-800 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-stone-800 dark:text-stone-100" x-text="'Transaksi ' + selectedBorrowing?.transaction_code"></h3>
                        <template x-if="selectedBorrowing?.raw_status === 'pending'">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                ● Menunggu Persetujuan Admin
                            </span>
                        </template>
                        <template x-if="selectedBorrowing?.raw_status === 'approved'">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                ✓ Disetujui
                            </span>
                        </template>
                        <template x-if="selectedBorrowing?.raw_status === 'borrowed' && !selectedBorrowing?.is_overdue">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                ● Dipinjam
                            </span>
                        </template>
                        <template x-if="selectedBorrowing?.raw_status === 'return_pending_verification'">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-300">
                                ● Menunggu Verifikasi Pengembalian
                            </span>
                        </template>
                        <template x-if="selectedBorrowing?.raw_status === 'returned'">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                ✓ Selesai
                            </span>
                        </template>
                        <template x-if="selectedBorrowing?.is_overdue">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                Overdue (Terlambat)
                            </span>
                        </template>
                    </div>
                    <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                        Rincian data transaksi, riwayat serah terima, dan aksi persetujuan
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeDetail()"
                    class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 p-1.5 rounded-lg hover:bg-stone-100 dark:hover:bg-stone-800 transition cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                {{-- Action Card: Persetujuan Request (Saat Status Pending) --}}
                <template x-if="selectedBorrowing?.raw_status === 'pending'">
                    <div class="bg-yellow-50/90 dark:bg-yellow-950/40 border border-yellow-200 dark:border-yellow-500/40 rounded-2xl p-4 sm:p-5">
                        <div class="flex items-center gap-2 text-xs font-bold text-yellow-900 dark:text-yellow-300 uppercase tracking-wider mb-2">
                            <svg class="w-4 h-4 text-yellow-700 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Tinjau Permohonan Peminjaman
                        </div>
                        <p class="text-xs text-yellow-800 dark:text-yellow-200 leading-relaxed mb-4">
                            Permohonan ini menunggu konfirmasi dari Admin. Jika disetujui, peminjam dapat datang ke ruangan TEFA untuk mengambil barang fisik dan melakukan verifikasi serah terima.
                        </p>

                        <div class="flex flex-wrap items-center gap-2.5">
                            {{-- Form Approve --}}
                            <form :action="'/admin/borrowings/' + selectedBorrowing?.id + '/approve'" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui Permintaan
                                </button>
                            </form>

                            {{-- Form Reject --}}
                            <button
                                type="button"
                                @click="rejectModalOpen = !rejectModalOpen"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Permintaan
                            </button>
                        </div>

                        {{-- Form Input Alasan Penolakan --}}
                        <div x-show="rejectModalOpen" class="mt-4 pt-3 border-t border-yellow-200 dark:border-yellow-500/30" x-cloak>
                            <form :action="'/admin/borrowings/' + selectedBorrowing?.id + '/reject'" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-rose-900 dark:text-rose-300 mb-1">Alasan Penolakan:</label>
                                    <input
                                        type="text"
                                        name="rejection_reason"
                                        required
                                        placeholder="Contoh: Unit sedang dijadwalkan untuk ujian praktikum..."
                                        class="w-full text-xs rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-3 py-2 text-stone-900 dark:text-stone-100"
                                    >
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="rejectModalOpen = false" class="px-3 py-1.5 rounded-lg border border-stone-300 text-xs">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-xs">Konfirmasi Tolak</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- Action Card: Verifikasi Pengembalian Fisik (Saat Status return_pending_verification) --}}
                <template x-if="selectedBorrowing?.raw_status === 'return_pending_verification'">
                    <div class="bg-purple-50/90 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-500/40 rounded-2xl p-4 sm:p-5">
                        <div class="flex items-center gap-2 text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-2">
                            <svg class="w-4 h-4 text-purple-700 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Verifikasi Pengembalian Fisik Unit
                        </div>
                        <p class="text-xs text-purple-800 dark:text-purple-200 leading-relaxed mb-4">
                            Peminjam telah menyerahkan barang fisik & mengambil foto bukti pengembalian. Periksa kondisi unit fisik lalu konfirmasi verifikasi untuk mengembalikan status unit ke katalog.
                        </p>

                        <form :action="'/admin/borrowings/' + selectedBorrowing?.id + '/verify-return'" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">Kondisi Fisik Saat Kembali:</label>
                                    <select name="return_condition" class="w-full text-xs rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-3 py-2">
                                        <option value="Baik">Baik (Normal)</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat (Masuk Perbaikan)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">Catatan Admin / Verifikator:</label>
                                    <input
                                        type="text"
                                        name="return_verification_note"
                                        placeholder="Contoh: Unit lengkap beserta charger dalam kondisi normal..."
                                        class="w-full text-xs rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-3 py-2"
                                    >
                                </div>
                            </div>
                            <div class="flex justify-end pt-2">
                                <button
                                    type="submit"
                                    class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verifikasi Pengembalian & Selesaikan
                                </button>
                            </div>
                        </form>
                    </div>
                </template>

                {{-- Informasi Peminjam & Barang --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- User --}}
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
                            <div class="flex items-center justify-between">
                                <span class="text-stone-400 dark:text-stone-500 text-[11px]">No. Telepon / HP:</span>
                                <span class="font-mono text-stone-700 dark:text-stone-300" x-text="selectedBorrowing?.borrower.formatted_phone || selectedBorrowing?.borrower.phone || '-'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Barang --}}
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

                {{-- Timeline Sirkulasi --}}
                <div class="bg-white dark:bg-[#131B2A] rounded-xl p-4 border border-stone-200 dark:border-stone-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-stone-700 dark:text-stone-300 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-[#6F4E37] dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Timeline Sirkulasi & Persetujuan
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div class="bg-stone-50 dark:bg-[#0E1420] p-2.5 rounded-lg border border-stone-100 dark:border-stone-800">
                            <span class="text-[10px] text-stone-400 dark:text-stone-500 block mb-0.5">Diajukan</span>
                            <span class="font-bold text-stone-800 dark:text-stone-200 text-xs" x-text="selectedBorrowing?.dates.requested_at || '-'"></span>
                        </div>

                        <div class="bg-stone-50 dark:bg-[#0E1420] p-2.5 rounded-lg border border-stone-100 dark:border-stone-800">
                            <span class="text-[10px] text-stone-400 dark:text-stone-500 block mb-0.5">Diserahterimakan</span>
                            <span class="font-bold text-stone-800 dark:text-stone-200 text-xs" x-text="selectedBorrowing?.dates.borrowed_at || '-'"></span>
                        </div>

                        <div class="p-2.5 rounded-lg border" :class="selectedBorrowing?.is_overdue ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-500/30' : 'bg-amber-50/60 dark:bg-amber-950/40 border-amber-200 dark:border-amber-500/30'">
                            <span class="text-[10px] block mb-0.5" :class="selectedBorrowing?.is_overdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-amber-800 dark:text-neon-glowamber'">
                                Target Kembali
                            </span>
                            <span class="font-bold text-xs" :class="selectedBorrowing?.is_overdue ? 'text-rose-700 dark:text-rose-300' : 'text-amber-900 dark:text-neon-glowamber'" x-text="selectedBorrowing?.dates.due_at"></span>
                        </div>

                        <div class="p-2.5 rounded-lg border" :class="selectedBorrowing?.dates.returned_at ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-500/30' : 'bg-stone-50 dark:bg-[#0E1420] border-stone-100 dark:border-stone-800'">
                            <span class="text-[10px] text-stone-400 dark:text-stone-500 block mb-0.5">Dikembalikan</span>
                            <span
                                class="font-bold text-xs"
                                :class="selectedBorrowing?.dates.returned_at ? 'text-emerald-800 dark:text-neon-emerald' : 'text-stone-400 dark:text-stone-500 italic'"
                                x-text="selectedBorrowing?.dates.returned_at || 'Belum'"
                            ></span>
                        </div>
                    </div>
                </div>

                {{-- Bukti Foto Real-time --}}
                <div class="bg-white dark:bg-[#131B2A] rounded-xl p-4 border border-stone-200 dark:border-stone-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-stone-700 dark:text-stone-300 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-[#6F4E37] dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Bukti Foto Serah Terima Real-Time
                        </div>
                        <span class="text-[11px] text-stone-400 dark:text-stone-500">Webcam Kamera</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Foto Saat Peminjaman --}}
                        <div>
                            <span class="text-[11px] font-semibold text-stone-600 dark:text-stone-400 block mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Bukti Saat Serah Terima
                            </span>

                            <template x-if="selectedBorrowing?.borrowing_evidence_url">
                                <div
                                    class="relative aspect-video rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 shadow-sm group cursor-pointer"
                                    @click="previewImage = selectedBorrowing?.borrowing_evidence_url"
                                >
                                    <img
                                        :src="selectedBorrowing?.borrowing_evidence_url"
                                        alt="Bukti Peminjaman"
                                        class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        Perbesar
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedBorrowing?.borrowing_evidence_url">
                                <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-stone-200 dark:border-stone-700 flex flex-col items-center justify-center text-stone-400 dark:text-stone-500 text-xs p-4 text-center">
                                    <span>Belum ada foto serah terima</span>
                                </div>
                            </template>
                        </div>

                        {{-- Foto Saat Pengembalian --}}
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
                                        class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        Perbesar
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedBorrowing?.return_evidence_url">
                                <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-stone-200 dark:border-stone-700 flex flex-col items-center justify-center text-stone-400 dark:text-stone-500 text-xs p-4 text-center">
                                    <span x-text="selectedBorrowing?.dates.returned_at ? 'Tidak ada foto bukti pengembalian' : 'Barang belum dikembalikan'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="bg-stone-50 dark:bg-[#0E1420] px-6 py-3.5 border-t border-stone-200 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[11px] text-stone-400 dark:text-stone-500 font-mono" x-text="'ID: #' + selectedBorrowing?.id"></span>
                <div class="flex items-center gap-2">
                    <template x-if="selectedBorrowing?.wa_url">
                        <a
                            :href="selectedBorrowing?.wa_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm active:scale-95"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Kirim WhatsApp
                        </a>
                    </template>
                    <button
                        type="button"
                        @click="closeDetail()"
                        class="px-5 py-2 rounded-xl bg-[#6F4E37] text-white text-xs font-bold hover:bg-[#5a3f2c] transition shadow-sm cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox Zoom Foto Bukti --}}
    <div
        x-show="previewImage !== null"
        class="fixed inset-0 z-[70] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window.stop="previewImage = null"
        @click="previewImage = null"
    >
        <div class="relative z-[80] max-w-4xl max-h-[90vh]" @click.stop>
            <img :src="previewImage" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl border border-white/20" alt="Preview Foto Bukti">
            <button
                type="button"
                @click="previewImage = null"
                class="absolute -top-10 right-0 text-white hover:text-white font-bold text-sm bg-black/50 hover:bg-black/75 px-3 py-1 rounded-lg backdrop-blur-md transition shadow-md cursor-pointer border border-white/20"
            >
                ✕ Tutup Gambar
            </button>
        </div>
    </div>

</div>

@endsection
