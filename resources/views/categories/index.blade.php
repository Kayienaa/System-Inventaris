@extends('layouts.app')

@section('title', 'Kategori Barang | TE-Vault')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-stone-800 dark:text-stone-100">Kategori Barang</h1>
            <p class="text-stone-500 dark:text-stone-400 mt-1">Daftar kategori inventaris TEFA</p>
        </div>

        @if ($categories->count())

            <div class="bg-white/95 dark:bg-[#131B2A]/90 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] overflow-hidden border border-stone-200/70 dark:border-stone-800/80">
                <table class="w-full text-left">
                    <thead class="bg-stone-50 dark:bg-stone-900/80 text-sm text-stone-500 dark:text-stone-400">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Nama Kategori</th>
                            <th class="px-5 py-3">Deskripsi</th>
                            <th class="px-5 py-3">Jumlah Barang</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-amber-50/20 dark:hover:bg-cyan-500/5 transition">
                                <td class="px-5 py-3 text-sm text-stone-600 dark:text-stone-300 font-mono">{{ $category->code }}</td>
                                <td class="px-5 py-3 font-medium text-stone-800 dark:text-stone-100">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-sm text-stone-500 dark:text-stone-400">{{ $category->description ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-stone-600 dark:text-stone-300">{{ $category->assets_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($category->is_active)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-neon-emerald border border-emerald-200 dark:border-emerald-500/30">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-stone-100 text-stone-600 dark:bg-stone-900/60 dark:text-stone-400 border border-stone-200 dark:border-stone-800">Nonaktif</span>
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

            <div class="bg-white/95 dark:bg-[#131B2A]/90 rounded-2xl p-10 text-center shadow-sm border border-stone-200/70 dark:border-stone-800/80">
                <p class="text-stone-500 dark:text-stone-400">Belum ada kategori.</p>
            </div>

        @endif

    </div>

@endsection
