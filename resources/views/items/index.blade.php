@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Katalog Barang
            </h1>

            <p class="mt-1 text-gray-500">
                Daftar inventaris TEFA SMKN 1 Bangsri
            </p>
        </div>

        {{-- Filter kategori --}}
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <a
                href="{{ route('items.index') }}"
                class="rounded-xl px-4 py-2 text-sm font-medium
                    {{ request('category')
                        ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        : 'bg-gray-800 text-white' }}"
            >
                Semua
            </a>

            <a
                href="{{ route('items.index', ['category' => 'Laptop']) }}"
                class="rounded-xl px-4 py-2 text-sm font-medium
                    {{ request('category') === 'Laptop'
                        ? 'bg-gray-800 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Laptop
            </a>

            <a
                href="{{ route('items.index', ['category' => 'Smartphone']) }}"
                class="rounded-xl px-4 py-2 text-sm font-medium
                    {{ request('category') === 'Smartphone'
                        ? 'bg-gray-800 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Smartphone
            </a>
        </div>

        {{-- Daftar Item --}}
        @if ($items->count())

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @foreach ($items as $item)

                    @php
                        $gambar = $item->gambar
                            ? basename(str_replace('\\', '/', $item->gambar))
                            : null;

                        $gambarPath = $gambar
                            ? 'items/' . $gambar
                            : null;

                        $gambarTersedia = $gambarPath
                            && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath);
                    @endphp

                    <article
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                    >

                        {{-- Foto --}}
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">

                            @if ($gambarTersedia)

                                <img
                                    src="{{ asset('storage/items/' . $gambar) }}"
                                    alt="{{ $item->nama_barang }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                >

                            @else

                                <div class="flex h-full flex-col items-center justify-center gap-2 text-gray-400">

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-dashed border-gray-300"
                                    >
                                        <span class="text-xl" aria-hidden="true">
                                            +
                                        </span>
                                    </div>

                                    <span class="text-sm">
                                        Foto tidak tersedia
                                    </span>

                                </div>

                            @endif

                        </div>

                        {{-- Informasi Item --}}
                        <div class="p-4">

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    {{-- Nama --}}
                                    <h2 class="text-lg font-semibold text-gray-800">
                                        {{ $item->nama_barang }}
                                    </h2>

                                    {{-- Kode --}}
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $item->kode_unik }}
                                    </p>

                                    {{-- Kategori --}}
                                    @if ($item->category)
                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ $item->category->nama }}
                                        </p>
                                    @endif

                                </div>

                                {{-- Status --}}
                                <span
                                    class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $item->status === 'Tersedia'
                                            ? 'bg-green-100 text-green-700'
                                            : ($item->status === 'Dipinjam'
                                                ? 'bg-red-100 text-red-700'
                                                : ($item->status === 'Maintenance'
                                                    ? 'bg-yellow-100 text-yellow-700'
                                                    : 'bg-gray-100 text-gray-600')) }}"
                                >
                                    {{ $item->status }}
                                </span>

                            </div>

                            {{-- Tombol peminjaman --}}
                            @if ($item->status === 'Tersedia')

                                <div class="mt-4">

                                    <a
                                        href="{{ route('items.borrow', $item) }}"
                                        class="block w-full rounded-xl bg-gray-800 px-4 py-2.5 text-center text-sm font-medium text-white transition hover:bg-gray-700"
                                    >
                                        Pinjam Barang
                                    </a>

                                </div>

                            @else

                                <div class="mt-4">

                                    <button
                                        type="button"
                                        disabled
                                        class="w-full cursor-not-allowed rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-400"
                                    >
                                        Tidak Tersedia
                                    </button>

                                </div>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $items->links() }}
            </div>

        @else

            {{-- Tidak ada item --}}
            <div class="rounded-2xl bg-white p-10 text-center shadow-sm">

                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100"
                >
                    <span class="text-2xl text-gray-400">
                        +
                    </span>
                </div>

                <p class="text-gray-500">
                    Belum ada barang yang tersedia.
                </p>

            </div>

        @endif

    </div>
@endsection