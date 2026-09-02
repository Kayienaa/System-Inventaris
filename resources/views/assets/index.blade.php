@extends('layouts.app')

@section('title', 'Katalog Barang | TE-Vault')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Header & Search --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Katalog Inventaris
                </h1>
                <p class="mt-1 text-gray-500">
                    Daftar alat dan perangkat inventaris TEFA SMKN 1 Bangsri
                </p>
            </div>

            {{-- Search Input Form --}}
            <form action="{{ route('assets.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, kode, atau merk..."
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm shadow-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600"
                    >
                    <svg class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button
                    type="submit"
                    class="rounded-xl bg-[#6F4E37] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#5a3f2c] transition shadow-sm"
                >
                    Cari
                </button>
            </form>
        </div>

        {{-- Filter Kategori --}}
        <div class="mb-6 flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('assets.index', array_filter(['search' => request('search')])) }}"
                class="rounded-xl px-4 py-2 text-sm font-medium transition shadow-sm
                    {{ !request('category')
                        ? 'bg-[#6F4E37] text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}"
            >
                Semua Kategori
            </a>

            @foreach ($categories as $cat)
                @php
                    $isActive = request('category') === $cat->name || request('category') === $cat->code;
                @endphp
                <a
                    href="{{ route('assets.index', array_filter(['category' => $cat->name, 'search' => request('search')])) }}"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition shadow-sm
                        {{ $isActive
                            ? 'bg-[#6F4E37] text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}"
                >
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Daftar Asset --}}
        @if ($assets->count())

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @foreach ($assets as $asset)

                    @php
                        $photoExists = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                        $statusValue = $asset->availability_status->value ?? (string) $asset->availability_status;
                    @endphp

                    <article
                        class="flex flex-col justify-between overflow-hidden rounded-2xl border shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md
                            {{ $statusValue === 'tersedia' ? 'border-gray-200 bg-white' : ($statusValue === 'dipinjam' ? 'border-rose-200 bg-rose-50/20' : 'border-gray-200 bg-gray-50/40') }}"
                    >

                        <div>
                            {{-- Foto Aset --}}
                            <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100 relative">

                                @if ($photoExists)
                                    <img
                                        src="{{ asset('storage/' . $asset->photo_path) }}"
                                        alt="{{ $asset->name }}"
                                        class="h-full w-full object-cover transition duration-300 {{ $statusValue !== 'tersedia' ? 'filter grayscale contrast-125 opacity-75' : '' }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex h-full flex-col items-center justify-center gap-2 text-gray-400 bg-gradient-to-br from-gray-50 to-gray-150">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 border border-gray-200 shadow-sm">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-gray-400">
                                            Foto belum tersedia
                                        </span>
                                    </div>
                                @endif

                                {{-- Status Badge Overlay --}}
                                <div class="absolute top-3 right-3">
                                    @if ($statusValue === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 shadow-sm border border-emerald-200">
                                            ● Tersedia
                                        </span>
                                    @elseif ($statusValue === 'dipesan')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 shadow-sm border border-amber-200">
                                            ● Dipesan
                                        </span>
                                    @elseif ($statusValue === 'dipinjam')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 shadow-sm border border-rose-200">
                                            ● Dipinjam
                                        </span>
                                    @elseif ($statusValue === 'perbaikan')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 shadow-sm border border-yellow-200">
                                            ● Perbaikan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 shadow-sm border border-gray-200">
                                            ● Tidak Tersedia
                                        </span>
                                    @endif
                                </div>

                            </div>

                            {{-- Informasi Aset --}}
                            <div class="p-5">

                                <div class="mb-2">
                                    @if ($asset->category)
                                        <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">
                                            {{ $asset->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-base font-bold text-gray-800 line-clamp-1" title="{{ $asset->name }}">
                                    {{ $asset->name }}
                                </h2>

                                <p class="mt-1 text-xs font-mono text-gray-500">
                                    {{ $asset->asset_code }}
                                </p>

                                @if ($asset->notes)
                                    <p class="mt-2 text-xs text-gray-500 line-clamp-2" title="{{ $asset->notes }}">
                                        {{ $asset->notes }}
                                    </p>
                                @endif

                                {{-- Jika sedang dipinjam, tampilkan info peminjam --}}
                                @if ($statusValue === 'dipinjam' && $asset->activeBorrowing)
                                    <div class="mt-3 p-2.5 bg-rose-50 border border-rose-100 rounded-xl text-xs text-rose-700">
                                        <p class="font-medium">Dipinjam oleh: {{ $asset->activeBorrowing->borrower->name ?? 'User' }}</p>
                                        @if ($asset->activeBorrowing->due_at)
                                            <p class="text-[11px] text-rose-500 mt-0.5">Kembali: {{ $asset->activeBorrowing->due_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="p-5 pt-0">
                            @if ($statusValue === 'tersedia')
                                <a
                                    href="{{ route('assets.borrow', $asset) }}"
                                    class="block w-full rounded-xl bg-[#6F4E37] px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-[#5a3f2c] shadow-sm active:scale-[0.98]"
                                >
                                    Pinjam Barang
                                </a>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="w-full cursor-not-allowed rounded-xl bg-gray-100 border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-400"
                                >
                                    {{ $statusValue === 'dipinjam' ? 'Sedang Dipinjam' : ($statusValue === 'dipesan' ? 'Sedang Dipesan' : 'Tidak Tersedia') }}
                                </button>
                            @endif
                        </div>

                    </article>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $assets->links() }}
            </div>

        @else

            {{-- Tidak ada aset --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-800">
                    Tidak Ada Aset Ditemukan
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Tidak ada barang inventaris yang sesuai dengan filter atau pencarian saat ini.
                </p>

                <div class="mt-6">
                    <a
                        href="{{ route('assets.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#6F4E37] px-4 py-2 text-sm font-medium text-white hover:bg-[#5a3f2c] transition shadow-sm"
                    >
                        Reset Filter
                    </a>
                </div>
            </div>

        @endif

    </div>
@endsection
