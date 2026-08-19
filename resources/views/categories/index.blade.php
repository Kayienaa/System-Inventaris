@extends('layouts.app')

@section('title', 'Kategori Barang | TE-Vault')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Kategori Barang</h1>
            <p class="text-gray-500 mt-1">Daftar kategori inventaris TEFA</p>
        </div>

        @if ($categories->count())

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-sm text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Nama Kategori</th>
                            <th class="px-5 py-3">Deskripsi</th>
                            <th class="px-5 py-3">Jumlah Barang</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $category->code }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $category->description ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $category->assets_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($category->is_active)
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $categories->links() }}
            </div>

        @else

            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <p class="text-gray-500">Belum ada kategori.</p>
            </div>

        @endif

    </div>

@endsection
