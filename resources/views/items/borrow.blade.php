@extends('layouts.app')

@section('title', 'Pinjam Barang')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-8">

        <div class="mb-8">
            <a
                href="{{ route('items.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800"
            >
                ← Kembali ke Katalog
            </a>

            <h1 class="mt-4 text-3xl font-bold text-gray-800">
                Pinjam Barang
            </h1>

            <p class="mt-1 text-gray-500">
                Lengkapi data peminjaman barang yang kamu pilih.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- Informasi barang --}}
            <div class="border-b border-gray-100 p-6">
                <div class="flex flex-col gap-5 sm:flex-row">

                    <div class="h-40 w-full overflow-hidden rounded-xl bg-gray-100 sm:w-52">
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

                        @if ($gambarTersedia)
                            <img
                                src="{{ asset('storage/items/' . $gambar) }}"
                                alt="{{ $item->nama_barang }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-gray-400">
                                Foto tidak tersedia
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            {{ $item->category?->nama }}
                        </p>

                        <h2 class="mt-1 text-xl font-semibold text-gray-800">
                            {{ $item->nama_barang }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $item->kode_unik }}
                        </p>

                        <span class="mt-3 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                            {{ $item->status }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Form --}}
            <form
                action="{{ route('items.borrow.store', $item) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6 p-6"
            >
                @csrf

                {{-- Tanggal kembali --}}
                <div>
                    <label
                        for="due_at"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Rencana Pengembalian
                    </label>

                    <input
                        id="due_at"
                        name="due_at"
                        type="datetime-local"
                        value="{{ old('due_at') }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        required
                        class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500"
                    >

                    @error('due_at')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Bukti peminjaman --}}
                <div>
                    <label
                        for="borrowing_evidence"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Foto/Bukti Peminjaman
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Upload foto sebagai bukti saat mengambil barang.
                    </p>

                    <input
                        id="borrowing_evidence"
                        name="borrowing_evidence"
                        type="file"
                        accept="image/*"
                        capture="environment"
                        required
                        class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm"
                    >

                    @error('borrowing_evidence')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label
                        for="borrower_note"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Catatan
                        <span class="font-normal text-gray-400">
                            (opsional)
                        </span>
                    </label>

                    <textarea
                        id="borrower_note"
                        name="borrower_note"
                        rows="4"
                        maxlength="1000"
                        placeholder="Tambahkan catatan jika diperlukan..."
                        class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500"
                    >{{ old('borrower_note') }}</textarea>

                    @error('borrower_note')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                @error('item')
                    <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Tombol --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('items.index') }}"
                        class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-gray-800 px-5 py-3 text-sm font-medium text-white hover:bg-gray-700"
                    >
                        Ajukan Peminjaman
                    </button>

                </div>

            </form>

        </div>
    </div>
@endsection