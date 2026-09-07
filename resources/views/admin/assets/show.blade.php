@extends('layouts.app')

@section('title', 'Detail Aset: ' . $asset->name . ' (' . $asset->asset_code . ') | SITEFA')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8" x-data="{ photoModal: false }">

    {{-- Breadcrumbs & Back Action --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-stone-500 dark:text-stone-400 mb-3">
            <a href="{{ route('admin.assets.index') }}" class="hover:text-amber-700 dark:hover:text-amber-400 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Master Aset
            </a>
            <span>/</span>
            <span class="text-stone-800 dark:text-stone-200 font-semibold truncate">Detail Aset ({{ $asset->asset_code }})</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 dark:text-stone-100 tracking-tight">
                        {{ $asset->name }}
                    </h1>
                    <span class="px-2.5 py-1 rounded-lg font-mono text-xs font-bold bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-300 dark:border-stone-700">
                        {{ $asset->asset_code }}
                    </span>
                </div>
                <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm mt-1">
                    {{ $asset->brand ?? 'Tanpa Merk' }} {{ $asset->model ? '• Model ' . $asset->model : '' }}
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a
                    href="{{ route('admin.assets.index') }}"
                    class="px-3.5 py-2 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-800 text-stone-700 dark:text-stone-300 text-xs font-semibold hover:bg-stone-50 dark:hover:bg-stone-700 transition shadow-sm"
                >
                    Daftar Aset
                </a>
                <a
                    href="{{ route('admin.assets.edit', $asset) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] dark:bg-amber-600 dark:hover:bg-amber-700 text-white text-xs font-semibold transition shadow-sm active:scale-95"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Informasi Aset
                </a>
            </div>
        </div>
    </div>

    {{-- Grid: Info & Spesifikasi Aset --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Kolom Kiri: Foto Aset & Status Kunci --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-[#131B2A] rounded-2xl border border-stone-200 dark:border-stone-800 p-5 shadow-sm overflow-hidden">
                <h2 class="text-xs font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500 mb-3">Foto Unit Fisik</h2>

                @php
                    $statusVal = $asset->availability_status->value ?? (string) $asset->availability_status;
                    $condVal = $asset->condition->value ?? (string) $asset->condition;
                @endphp

                @if($asset->photo_url)
                    <div class="relative group rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 aspect-video cursor-pointer" @click="photoModal = true">
                        <img
                            src="{{ $asset->photo_url }}"
                            alt="{{ $asset->name }}"
                            loading="lazy"
                            decoding="async"
                            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div class="hidden w-full h-full flex flex-col items-center justify-center p-4 text-center">
                            <svg class="w-10 h-10 mb-2 stroke-[1.25] text-stone-400 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-xs font-medium text-stone-400 dark:text-stone-500">Belum ada foto unit</span>
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                            Perbesar Foto
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border-2 border-dashed border-stone-200 dark:border-stone-800 aspect-video flex flex-col items-center justify-center text-stone-400 dark:text-stone-600 bg-stone-50/50 dark:bg-[#0E1420]">
                        <svg class="w-10 h-10 mb-2 stroke-[1.25]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs font-medium">Belum ada foto unit</span>
                    </div>
                @endif

                {{-- Status & Kondisi Badges --}}
                <div class="mt-4 pt-4 border-t border-stone-100 dark:border-stone-800 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-stone-400 dark:text-stone-500 block text-[11px] mb-1">Status Ketersediaan</span>
                        @if ($statusVal === 'tersedia')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Tersedia
                            </span>
                        @elseif ($statusVal === 'dipinjam')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Dipinjam
                            </span>
                        @elseif ($statusVal === 'perbaikan')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Perbaikan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800/80">
                                {{ ucfirst(str_replace('_', ' ', $statusVal)) }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="text-stone-400 dark:text-stone-500 block text-[11px] mb-1">Kondisi Fisik</span>
                        @if ($condVal === 'baik')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Baik
                            </span>
                        @elseif ($condVal === 'rusak_ringan')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Rusak Ringan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Rusak Berat
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kartu Peminjam Terkini (jika sedang dipinjam) --}}
            @if($asset->activeBorrowing)
                <div class="bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-bold text-xs uppercase tracking-wider mb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        Peminjaman Aktif Terkini
                    </div>
                    <p class="text-sm font-bold text-stone-900 dark:text-stone-100">
                        {{ $asset->activeBorrowing->borrower->name ?? 'User' }}
                    </p>
                    <div class="text-xs text-stone-600 dark:text-stone-400 space-y-1 mt-1.5">
                        @if($asset->activeBorrowing->borrower?->siswaProfile?->class_name)
                            <p>Kelas: <span class="font-semibold text-stone-800 dark:text-stone-200">{{ $asset->activeBorrowing->borrower->siswaProfile->class_name }}</span></p>
                        @endif
                        <p>Target Kembali: <span class="font-semibold text-amber-700 dark:text-amber-400">{{ $asset->activeBorrowing->due_at ? $asset->activeBorrowing->due_at->format('d M Y H:i') : '-' }}</span></p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-amber-200/60 dark:border-amber-800/40 flex items-center justify-between">
                        <a
                            href="{{ route('admin.borrowings.show', $asset->activeBorrowing) }}"
                            class="text-xs font-bold text-[#6F4E37] dark:text-amber-400 hover:underline flex items-center gap-1"
                        >
                            Rincian Transaksi
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        @php
                            $waUrl = app(\App\Services\WhatsAppNotificationService::class)->getWhatsAppUrl($asset->activeBorrowing);
                        @endphp
                        @if($waUrl)
                            <a
                                href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold transition"
                                title="Kirim Pesan WhatsApp"
                            >
                                WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Rincian Spesifikasi Teknis --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#131B2A] rounded-2xl border border-stone-200 dark:border-stone-800 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-stone-900 dark:text-stone-100 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#6F4E37] dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Spesifikasi & Informasi Master
                </h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Nama Aset</dt>
                        <dd class="text-stone-900 dark:text-stone-100 font-bold text-sm mt-0.5">{{ $asset->name }}</dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Kode Unik Aset</dt>
                        <dd class="text-stone-900 dark:text-stone-100 font-mono font-bold text-sm mt-0.5">{{ $asset->asset_code }}</dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Kategori Inventaris</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-semibold mt-0.5">
                            {{ $asset->category?->name ?? 'Tidak Berkategori' }}
                        </dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Nomor Seri (Serial Number)</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-mono font-semibold mt-0.5">
                            {{ $asset->serial_number ?: '-' }}
                        </dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Merk / Pabrikan</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-semibold mt-0.5">
                            {{ $asset->brand ?: '-' }}
                        </dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Model / Tipe</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-semibold mt-0.5">
                            {{ $asset->model ?: '-' }}
                        </dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Tanggal Masuk / Terdaftar</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-semibold mt-0.5">
                            {{ $asset->created_at ? $asset->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                        </dd>
                    </div>

                    <div class="border-b border-stone-100 dark:border-stone-800/80 pb-2.5">
                        <dt class="text-stone-400 dark:text-stone-500 font-medium">Terakhir Diperbarui</dt>
                        <dd class="text-stone-800 dark:text-stone-200 font-semibold mt-0.5">
                            {{ $asset->updated_at ? $asset->updated_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                        </dd>
                    </div>
                </dl>

                {{-- Catatan / Spesifikasi Lengkap --}}
                <div class="mt-5 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <h3 class="text-xs font-bold text-stone-500 dark:text-stone-400 mb-1.5">Catatan Spesifikasi / Keterangan</h3>
                    <div class="p-3.5 rounded-xl bg-stone-50 dark:bg-[#0E1420] border border-stone-200/80 dark:border-stone-800 text-stone-700 dark:text-stone-300 text-xs leading-relaxed whitespace-pre-line">
                        {{ $asset->notes ?: 'Tidak ada catatan tambahan untuk aset ini.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Peminjaman (Borrowing History) --}}
    <div class="bg-white dark:bg-[#131B2A] rounded-2xl border border-stone-200 dark:border-stone-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-stone-200 dark:border-stone-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-stone-900 dark:text-stone-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#6F4E37] dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Transaksi Peminjaman Unit
                </h2>
                <p class="text-stone-400 dark:text-stone-500 text-xs mt-0.5">
                    Daftar seluruh pengguna (siswa & guru) yang pernah atau sedang meminjam unit aset ini.
                </p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 self-start sm:self-auto">
                Total Riwayat: {{ $borrowingHistory->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600 dark:text-stone-300">
                <thead class="bg-stone-50/80 dark:bg-[#0E1420] border-b border-stone-200 dark:border-stone-800 text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                    <tr>
                        <th class="py-3 px-4">Peminjam</th>
                        <th class="py-3 px-4">Waktu Pinjam</th>
                        <th class="py-3 px-4">Tenggat Waktu</th>
                        <th class="py-3 px-4">Waktu Kembali</th>
                        <th class="py-3 px-4">Status Transaksi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                    @forelse($borrowingHistory as $b)
                        @php
                            $bStatus = $b->status->value ?? (string) $b->status;
                            $borrower = $b->borrower;
                            $nisOrNip = $borrower?->siswaProfile?->nis ?: ($borrower?->guruProfile?->nip ?: null);
                            $className = $borrower?->siswaProfile?->class_name;
                        @endphp
                        <tr class="hover:bg-stone-50/50 dark:hover:bg-stone-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div>
                                    <p class="font-bold text-stone-900 dark:text-stone-100">
                                        {{ $borrower->name ?? 'User Terhapus' }}
                                    </p>
                                    <p class="text-[11px] text-stone-400 dark:text-stone-500">
                                        @if($nisOrNip)
                                            <span>{{ $nisOrNip }}</span>
                                        @endif
                                        @if($className)
                                            <span>• {{ $className }}</span>
                                        @endif
                                    </p>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-medium text-stone-800 dark:text-stone-200">
                                    {{ $b->borrowed_at ? $b->borrowed_at->format('d M Y') : ($b->requested_at ? $b->requested_at->format('d M Y') : '-') }}
                                </span>
                                <span class="block text-[10px] text-stone-400 dark:text-stone-500">
                                    {{ $b->borrowed_at ? $b->borrowed_at->format('H:i') . ' WIB' : '' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-medium text-stone-800 dark:text-stone-200">
                                    {{ $b->due_at ? $b->due_at->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($b->returned_at)
                                    <span class="font-medium text-emerald-700 dark:text-emerald-400">
                                        {{ $b->returned_at->format('d M Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-stone-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($bStatus === 'returned')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                        ✓ Selesai
                                    </span>
                                @elseif($bStatus === 'borrowed')
                                    @if($b->due_at && now()->isAfter($b->due_at))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            ● Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800">
                                            ● Dipinjam
                                        </span>
                                    @endif
                                @elseif($bStatus === 'return_pending_verification')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                        Verifikasi Kembali
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-stone-100 text-stone-700 dark:bg-stone-800 dark:text-stone-300">
                                        {{ ucfirst($bStatus) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <a
                                    href="{{ route('admin.borrowings.show', $b) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700 font-semibold text-[11px] transition shadow-xs"
                                    title="Lihat Rincian Transaksi Peminjaman"
                                >
                                    Rincian
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400 dark:text-stone-500">
                                <div class="w-12 h-12 rounded-2xl bg-stone-50 dark:bg-stone-800/60 border border-stone-200 dark:border-stone-700 flex items-center justify-center mx-auto mb-2 text-stone-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold">Belum ada riwayat peminjaman untuk unit ini.</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">Seluruh peminjaman yang diajukan oleh siswa atau guru akan tercatat otomatis di tabel ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($borrowingHistory->hasPages())
            <div class="p-4 border-t border-stone-100 dark:border-stone-800">
                {{ $borrowingHistory->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Preview Foto --}}
    @if($asset->photo_url)
        <div
            x-show="photoModal"
            x-cloak
            @keydown.escape.window="photoModal = false"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="relative z-[80] max-w-3xl w-full bg-white dark:bg-stone-900 rounded-2xl overflow-hidden shadow-2xl border border-stone-800" @click.outside="photoModal = false">
                <div class="p-3 bg-stone-900 text-white flex items-center justify-between">
                    <span class="text-xs font-bold">{{ $asset->name }} ({{ $asset->asset_code }})</span>
                    <button type="button" @click="photoModal = false" class="text-stone-400 hover:text-white text-sm cursor-pointer">✕</button>
                </div>
                <img src="{{ $asset->photo_url }}" alt="{{ $asset->name }}" class="w-full max-h-[80vh] object-contain bg-black">
            </div>
        </div>
    @endif

</div>
@endsection
