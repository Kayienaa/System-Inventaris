@extends('layouts.app')

@section('title', 'Katalog Barang | TE-Vault')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Header & Search --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-stone-800 dark:text-stone-100">
                    Katalog Inventaris
                </h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">
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
                        class="w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-4 py-2.5 pl-10 text-sm text-stone-900 dark:text-stone-100 shadow-sm focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition"
                    >
                    <svg class="absolute left-3.5 top-3 h-4 w-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button
                    type="submit"
                    class="rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber px-4 py-2.5 text-sm font-medium transition-all duration-200 shadow-sm shrink-0"
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
                        ? 'bg-[#6F4E37] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:shadow-neon-amber'
                        : 'bg-white/95 dark:bg-[#131B2A]/90 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 border border-stone-200/70 dark:border-stone-800/80' }}"
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
                            ? 'bg-[#6F4E37] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:shadow-neon-amber'
                            : 'bg-white/95 dark:bg-[#131B2A]/90 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 border border-stone-200/70 dark:border-stone-800/80' }}"
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
                        class="flex flex-col justify-between overflow-hidden bg-white/95 dark:bg-[#131B2A]/90 backdrop-blur-md border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] hover:border-[#6F4E37] dark:hover:border-cyan-500/50 dark:hover:shadow-neon-sm transition-all duration-300 hover:-translate-y-1"
                    >

                        <div>
                            {{-- Foto Aset --}}
                            <div class="aspect-[4/3] w-full overflow-hidden bg-stone-100 dark:bg-stone-800 relative">

                                @if ($photoExists)
                                    <img
                                        src="{{ asset('storage/' . $asset->photo_path) }}"
                                        alt="{{ $asset->name }}"
                                        width="400"
                                        height="300"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover aspect-[4/3] transition duration-300 {{ $statusValue !== 'tersedia' ? 'filter grayscale contrast-125 opacity-75' : '' }}"
                                    >
                                @else
                                    <div class="flex h-full w-full aspect-[4/3] flex-col items-center justify-center gap-2 text-stone-400 bg-stone-100 dark:bg-stone-800">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 dark:bg-stone-700/80 border border-stone-200 dark:border-stone-600 shadow-sm">
                                            <svg class="w-6 h-6 text-stone-400 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-stone-400 dark:text-stone-300">
                                             Foto belum tersedia
                                        </span>
                                    </div>
                                @endif

                                {{-- Status Badge Overlay --}}
                                <div class="absolute top-3 right-3">
                                    @if ($statusValue === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-neon-emerald dark:border dark:border-emerald-500/30 shadow-sm border border-emerald-200">
                                            ● Tersedia
                                        </span>
                                    @elseif ($statusValue === 'dipesan')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-neon-glowamber dark:border dark:border-amber-500/30 shadow-sm border border-amber-200">
                                            ● Dipesan
                                        </span>
                                    @elseif ($statusValue === 'dipinjam')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-neon-glowamber dark:border dark:border-amber-500/30 shadow-sm border border-amber-200">
                                            ● Dipinjam
                                        </span>
                                    @elseif ($statusValue === 'perbaikan')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 dark:border dark:border-rose-500/40 shadow-sm border border-rose-200">
                                            ● Perbaikan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-stone-100 text-stone-700 dark:bg-stone-900/60 dark:text-stone-300 shadow-sm border border-stone-200 dark:border-stone-800">
                                            ● Tidak Tersedia
                                        </span>
                                    @endif
                                </div>

                            </div>

                            {{-- Informasi Aset --}}
                            <div class="p-5">

                                <div class="mb-2">
                                    @if ($asset->category)
                                        <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-700 dark:text-neon-glowamber bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-md border border-amber-100 dark:border-amber-500/30">
                                            {{ $asset->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-base font-bold text-stone-800 dark:text-stone-100 line-clamp-1" title="{{ $asset->name }}">
                                    {{ $asset->name }}
                                </h2>

                                <p class="mt-1 text-xs font-mono text-stone-500 dark:text-stone-400">
                                    {{ $asset->asset_code }}
                                </p>

                                @if ($asset->notes)
                                    <p class="mt-2 text-xs text-stone-500 dark:text-stone-400 line-clamp-2" title="{{ $asset->notes }}">
                                        {{ $asset->notes }}
                                    </p>
                                @endif

                                {{-- Jika sedang dipinjam, tampilkan info peminjam --}}
                                @if ($statusValue === 'dipinjam' && $asset->activeBorrowing)
                                    <div class="mt-3 p-2.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40 rounded-xl text-xs text-amber-800 dark:text-amber-200">
                                        <p class="font-medium">Dipinjam oleh: {{ $asset->activeBorrowing->borrower->name ?? 'User' }}</p>
                                        @if ($asset->activeBorrowing->due_at)
                                            <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">Kembali: {{ $asset->activeBorrowing->due_at->format('d M Y, H:i') }}</p>
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
                                    class="block w-full rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber px-4 py-2.5 text-center text-sm font-semibold transition-all duration-200 shadow-sm active:scale-[0.98]"
                                >
                                    Pinjam Barang
                                </a>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="w-full cursor-not-allowed rounded-xl bg-stone-100 dark:bg-stone-900/60 border border-stone-200 dark:border-stone-800 px-4 py-2.5 text-sm font-medium text-stone-400 dark:text-stone-500"
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
            <div class="rounded-2xl border border-stone-200/70 dark:border-stone-800/80 bg-white/95 dark:bg-[#131B2A]/90 p-12 text-center shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)]">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-neon-glowamber border border-amber-100 dark:border-amber-500/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-stone-800 dark:text-stone-100">
                    Tidak Ada Aset Ditemukan
                </h3>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                    Tidak ada barang inventaris yang sesuai dengan filter atau pencarian saat ini.
                </p>

                <div class="mt-6">
                    <a
                        href="{{ route('assets.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber px-4 py-2 text-sm font-medium transition-all duration-200 shadow-sm"
                    >
                        Reset Filter
                    </a>
                </div>
            </div>

        @endif

    </div>
@endsection
