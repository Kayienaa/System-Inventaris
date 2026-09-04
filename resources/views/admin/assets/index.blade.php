@extends('layouts.app')

@section('title', 'Manajemen Master Aset | TE-Vault')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8" x-data="{ deleteModalOpen: false, deleteUrl: '', deleteAssetName: '', deleteAssetCode: '' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                    Manajemen Master Aset
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                    Super Admin
                </span>
            </div>
            <p class="text-gray-500 text-sm mt-1">
                Kelola katalog inventaris perangkat, nomor seri, status ketersediaan, dan log riwayat aset TEFA SMKN 1 Bangsri.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.assets.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white text-sm font-semibold shadow-md shadow-[#6F4E37]/20 transition-all duration-150 active:scale-95"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Unit Aset
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Quick Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-[#6F4E37] shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Unit Aset</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Tersedia</p>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['tersedia'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['dipinjam'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Perbaikan / Rusak</p>
                <p class="text-2xl font-bold text-rose-600">{{ number_format($stats['perbaikan'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" action="{{ route('admin.assets.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            
            {{-- Kolom 1: Pencarian Kata Kunci (md:col-span-5) --}}
            <div class="md:col-span-5">
                <label for="search" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Pencarian Kata Kunci
                </label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nama aset, kode, serial number, merk..."
                        class="pl-10 pr-4 h-11 w-full rounded-xl border border-gray-300 focus:border-[#6F4E37] focus:ring-1 focus:ring-[#6F4E37] text-sm"
                    >
                </div>
            </div>

            {{-- Kolom 2: Kategori Aset (md:col-span-3) --}}
            <div class="md:col-span-3">
                <label for="category" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Kategori Aset
                </label>
                <select
                    id="category"
                    name="category"
                    class="h-11 w-full px-3 text-sm rounded-xl border border-gray-300 focus:border-[#6F4E37] focus:ring-1 focus:ring-[#6F4E37] shadow-sm bg-white"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 3: Status Ketersediaan (md:col-span-2) --}}
            <div class="md:col-span-2">
                <label for="availability_status" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Status Ketersediaan
                </label>
                <select
                    id="availability_status"
                    name="availability_status"
                    class="h-11 w-full px-3 text-sm rounded-xl border border-gray-300 focus:border-[#6F4E37] focus:ring-1 focus:ring-[#6F4E37] shadow-sm bg-white"
                >
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('availability_status') === $st->value ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $st->value)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 4: Tombol Filter & Reset (md:col-span-2 flex items-center gap-2) --}}
            <div class="md:col-span-2 flex items-center gap-2">
                <button
                    type="submit"
                    class="flex-1 h-11 px-4 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white flex items-center justify-center gap-2 text-sm font-semibold transition shadow-sm active:scale-95"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'category', 'availability_status', 'condition']))
                    <a
                        href="{{ route('admin.assets.index') }}"
                        class="h-11 px-3.5 rounded-xl border border-gray-300 bg-gray-50 text-gray-600 hover:bg-gray-100 flex items-center justify-center text-sm font-medium transition active:scale-95 shrink-0"
                        title="Reset Filter"
                    >
                        <span>Reset</span>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Asset Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3.5 px-4">Unit Aset</th>
                        <th class="py-3.5 px-4">Kode & No. Seri</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status Ketersediaan</th>
                        <th class="py-3.5 px-4">Peminjam Terkini</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($assets as $asset)
                        @php
                            $statusVal = $asset->availability_status->value ?? (string) $asset->availability_status;
                            $condVal = $asset->condition->value ?? (string) $asset->condition;
                            $hasPhoto = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                        @endphp
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            {{-- Info Unit Aset (Thumbnail & Name) --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-stone-100 dark:bg-stone-800 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center aspect-square">
                                        @if ($hasPhoto)
                                            <img
                                                src="{{ asset('storage/' . $asset->photo_path) }}"
                                                alt="{{ $asset->name }}"
                                                width="48"
                                                height="48"
                                                loading="lazy"
                                                decoding="async"
                                                class="w-full h-full object-cover aspect-square"
                                            >
                                        @else
                                            <svg class="w-6 h-6 text-stone-400 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm hover:text-amber-700 transition">
                                            {{ $asset->name }}
                                        </p>
                                        <p class="text-gray-400 text-[11px]">
                                            {{ $asset->brand ?? 'Tanpa Merk' }} {{ $asset->model ? '• ' . $asset->model : '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kode & No Seri --}}
                            <td class="py-3 px-4 font-mono">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-semibold text-[11px] border border-gray-200">
                                    {{ $asset->asset_code }}
                                </span>
                                <p class="text-gray-400 text-[11px] mt-1">
                                    SN: {{ $asset->serial_number ?? '-' }}
                                </p>
                            </td>

                            {{-- Kategori --}}
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-[#6F4E37] border border-amber-200">
                                    {{ $asset->category->name ?? '-' }}
                                </span>
                            </td>

                            {{-- Kondisi --}}
                            <td class="py-3 px-4">
                                @if ($condVal === 'baik')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Baik
                                    </span>
                                @elseif ($condVal === 'rusak_ringan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Rusak Ringan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Rusak Berat
                                    </span>
                                @endif
                            </td>

                            {{-- Status Ketersediaan --}}
                            <td class="py-3 px-4">
                                @if ($statusVal === 'tersedia')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Tersedia
                                    </span>
                                @elseif ($statusVal === 'dipinjam')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                        Dipinjam
                                    </span>
                                @elseif ($statusVal === 'perbaikan')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Perbaikan
                                    </span>
                                @elseif ($statusVal === 'dipesan')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Dipesan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                        Tidak Tersedia
                                    </span>
                                @endif
                            </td>

                            {{-- Peminjam Terkini --}}
                            <td class="py-3 px-4">
                                @if ($asset->activeBorrowing)
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $asset->activeBorrowing->borrower->name ?? 'User' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            Kembali: {{ $asset->activeBorrowing->due_at ? $asset->activeBorrowing->due_at->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a
                                        href="{{ route('admin.assets.edit', $asset) }}"
                                        class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-amber-700 hover:border-amber-300 hover:bg-amber-50 transition"
                                        title="Edit Informasi Aset"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <button
                                        type="button"
                                        @click="
                                            deleteModalOpen = true;
                                            deleteUrl = '{{ route('admin.assets.destroy', $asset) }}';
                                            deleteAssetName = '{{ addslashes($asset->name) }}';
                                            deleteAssetCode = '{{ addslashes($asset->asset_code) }}';
                                        "
                                        class="p-1.5 rounded-lg border border-gray-200 text-rose-500 hover:text-rose-700 hover:border-rose-300 hover:bg-rose-50 transition"
                                        title="Hapus Aset"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-[#6F4E37] mx-auto mb-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700">Tidak ada aset inventaris yang ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                                    Silakan sesuaikan kriteria pencarian atau tambahkan unit aset baru melalui tombol Tambah Unit Aset di atas.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($assets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    <div
        x-show="deleteModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div
            @click.away="deleteModalOpen = false"
            class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-md w-full p-6 text-left"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus Aset</h3>
            <p class="text-xs text-gray-500 mt-1">
                Apakah Anda yakin ingin menghapus aset berikut dari master inventaris?
            </p>

            <div class="my-4 p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs">
                <p class="font-bold text-gray-800" x-text="deleteAssetName"></p>
                <p class="text-gray-500 font-mono mt-0.5">Kode: <span x-text="deleteAssetCode"></span></p>
            </div>

            <p class="text-[11px] text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                <span class="font-semibold">Catatan:</span> Sistem akan otomatis memblokir penghapusan jika aset saat ini sedang memiliki riwayat peminjaman aktif.
            </p>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="deleteModalOpen = false"
                    class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition"
                >
                    Batal
                </button>

                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-sm transition"
                    >
                        Ya, Hapus Aset
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
