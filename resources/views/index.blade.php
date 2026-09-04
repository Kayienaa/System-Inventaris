<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Barang - TEFA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Katalog Barang
            </h1>

            <p class="text-gray-500 mt-1">
                Daftar inventaris TEFA SMKN 1 Bangsri
            </p>
        </div>

        {{-- Daftar Barang --}}
        @if ($assets->count())

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @foreach ($assets as $asset)

                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">

                        {{-- Foto Barang --}}
                        <div class="aspect-[4/3] bg-stone-100 dark:bg-stone-800 overflow-hidden relative">

                            @if ($asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path))

                                <img
                                    src="{{ asset('storage/' . $asset->photo_path) }}"
                                    alt="{{ $asset->name }}"
                                    width="400"
                                    height="300"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover aspect-[4/3]"
                                >

                            @else

                                <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 dark:text-stone-300 gap-1">
                                    <svg class="w-6 h-6 text-stone-300 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">Foto tidak tersedia</span>
                                </div>

                            @endif

                        </div>

                        {{-- Informasi Barang --}}
                        <div class="p-4">

                            <div class="flex items-start justify-between gap-3">

                                <div>
                                    <h2 class="font-semibold text-lg text-gray-800">
                                        {{ $asset->name }}
                                    </h2>

                                    <p class="text-sm text-gray-500">
                                        {{ $asset->asset_code }}
                                    </p>

                                    @if ($asset->category)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $asset->category->name }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Status --}}
                                @if ($asset->availability_status === 'tersedia')

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Tersedia
                                    </span>

                                @elseif ($asset->availability_status === 'dipinjam')

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                        Dipinjam
                                    </span>

                                @else

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $asset->availability_status)) }}
                                    </span>

                                @endif

                            </div>

                            {{-- Jika sedang dipinjam, tampilkan peminjam aktif --}}
                            @if ($asset->availability_status === 'dipinjam' && $asset->activeBorrowing)

                                <div class="mt-4 p-3 bg-red-50 rounded-xl">

                                    <p class="text-xs text-red-500">
                                        Sedang dipinjam oleh
                                    </p>

                                    <p class="font-medium text-red-700">
                                        {{ $asset->activeBorrowing->borrower->name }}
                                    </p>

                                    <p class="text-xs text-red-500 mt-1">
                                        Kembali: {{ $asset->activeBorrowing->due_at->format('d M Y, H:i') }}
                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

            <div class="mt-8">
                {{ $assets->links() }}
            </div>

        @else

            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">

                <p class="text-gray-500">
                    Belum ada barang yang tersedia.
                </p>

            </div>

        @endif

    </div>

</body>
</html>