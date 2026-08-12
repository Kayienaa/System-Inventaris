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
        @if ($items->count())

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @foreach ($items as $item)

                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">

                        {{-- Foto Barang --}}
                        <div class="aspect-[4/3] bg-gray-100 overflow-hidden">

                            @if (!empty($item['gambar']))

                                <img
                                    src="{{ asset('storage/' . $item['gambar']) }}"
                                    alt="{{ $item['nama_barang'] }}"
                                    class="w-full h-full object-cover"
                                >

                            @else

                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    Tidak ada foto
                                </div>

                            @endif

                        </div>

                        {{-- Informasi Barang --}}
                        <div class="p-4">

                            <div class="flex items-start justify-between gap-3">

                                <div>
                                    <h2 class="font-semibold text-lg text-gray-800">
                                        {{ $item['nama_barang'] }}
                                    </h2>

                                    <p class="text-sm text-gray-500">
                                        {{ $item['kode_unik'] }}
                                    </p>
                                </div>

                                {{-- Status --}}
                                @if ($item['status'] === 'Tersedia')

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Tersedia
                                    </span>

                                @elseif ($item['status'] === 'Dipinjam')

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                        Dipinjam
                                    </span>

                                @else

                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                        {{ $item['status'] }}
                                    </span>

                                @endif

                            </div>

                            {{-- Jika sedang dipinjam --}}
                            @if ($item['status'] === 'Dipinjam' && $item['borrower_name'])

                                <div class="mt-4 p-3 bg-red-50 rounded-xl">

                                    <p class="text-xs text-red-500">
                                        Sedang dipinjam oleh
                                    </p>

                                    <p class="font-medium text-red-700">
                                        {{ $item['borrower_name'] }}
                                    </p>

                                    @if ($item['due_date'])
                                        <p class="text-xs text-red-500 mt-1">
                                            Kembali: {{ $item['due_date'] }}
                                        </p>
                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

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