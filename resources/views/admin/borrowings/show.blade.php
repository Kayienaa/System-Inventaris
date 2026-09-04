@extends('layouts.app')

@section('title', 'Detail Transaksi Peminjaman ' . $detail['transaction_code'] . ' | TE-Vault')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8" x-data="{ previewImage: null }">

    {{-- Breadcrumb & Back --}}
    <div class="mb-6">
        <a
            href="{{ route('admin.borrowings.index') }}"
            class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-800 transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Pusat Monitoring Peminjaman
        </a>

        <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Transaksi {{ $detail['transaction_code'] }}
                </h1>
                @if($detail['is_overdue'])
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        ● Overdue (Terlambat)
                    </span>
                @elseif($detail['status'] === 'returned')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        ✓ Selesai
                    </span>
                @elseif($detail['status'] === 'borrowed')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        ● Sedang Dipinjam
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300">
                        {{ ucfirst($detail['status']) }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.borrowings.export-pdf') }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#6F4E37] text-white text-xs font-semibold hover:bg-[#5a3f2c] transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Laporan PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="p-6 sm:p-8 space-y-6">

            {{-- 1. Peminjam & Barang --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Peminjam --}}
                <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-200/80">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-[#6F4E37] uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Identitas Peminjam
                    </div>

                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-400 text-[11px]">Nama Lengkap:</span>
                            <p class="font-bold text-gray-800 text-base">{{ $detail['borrower']['name'] }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-gray-400">Role Sistem:</span>
                            <span class="font-semibold px-2 py-0.5 rounded text-[11px] bg-white border border-gray-200">{{ $detail['borrower']['role'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Identitas (NIS/NIP):</span>
                            <span class="font-mono font-bold text-gray-800">{{ $detail['borrower']['identity'] }}</span>
                        </div>
                        @if($detail['borrower']['class_name'])
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Kelas:</span>
                                <span class="font-medium text-gray-700">{{ $detail['borrower']['class_name'] }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Email:</span>
                            <span class="text-gray-600">{{ $detail['borrower']['email'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">No. Telepon / HP:</span>
                            <span class="text-gray-600">{{ $detail['borrower']['phone'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Barang Inventaris --}}
                <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-200/80">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-[#6F4E37] uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Barang Fisik
                    </div>

                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-400 text-[11px]">Nama Aset:</span>
                            <p class="font-bold text-gray-800 text-base">{{ $detail['asset']['name'] }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-gray-400">Kode Aset:</span>
                            <span class="font-mono font-bold text-amber-900 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded text-[11px]">
                                {{ $detail['asset']['asset_code'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Merk / Model:</span>
                            <span class="font-medium text-gray-700">{{ $detail['asset']['brand'] }} {{ $detail['asset']['model'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Serial Number:</span>
                            <span class="font-mono text-gray-700">{{ $detail['asset']['serial_number'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Kategori:</span>
                            <span class="font-semibold text-gray-600">{{ $detail['asset']['category'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Timeline Sirkulasi --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-3 text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-[#6F4E37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Waktu & Tenggat Peminjaman
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[11px] text-gray-400 block mb-1">Waktu Pinjam</span>
                        <span class="font-bold text-gray-800 text-sm">{{ $detail['dates']['borrowed_at'] }}</span>
                    </div>

                    <div class="p-4 rounded-xl border {{ $detail['is_overdue'] ? 'bg-rose-50 border-rose-200' : 'bg-amber-50/60 border-amber-200' }}">
                        <span class="text-[11px] block mb-1 {{ $detail['is_overdue'] ? 'text-rose-600 font-bold' : 'text-amber-800 font-semibold' }}">
                            Target Kembali (H+3)
                        </span>
                        <span class="font-bold text-sm {{ $detail['is_overdue'] ? 'text-rose-700' : 'text-amber-900' }}">
                            {{ $detail['dates']['due_at'] }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl border {{ $detail['dates']['returned_at'] ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-100' }}">
                        <span class="text-[11px] text-gray-400 block mb-1">Waktu Kembali Fisik</span>
                        <span class="font-bold text-sm {{ $detail['dates']['returned_at'] ? 'text-emerald-800' : 'text-gray-400 italic' }}">
                            {{ $detail['dates']['returned_at'] ?? 'Belum Dikembalikan' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Catatan --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-[#6F4E37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Catatan Peminjam
                </div>
                <div class="bg-gray-50 rounded-xl p-3.5 text-xs text-gray-700 border border-gray-100 leading-relaxed">
                    {{ $detail['borrower_note'] }}
                </div>

                @if($detail['return_note'])
                    <div class="mt-4">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block mb-1.5">Catatan Pengembalian:</span>
                        <div class="bg-emerald-50 rounded-xl p-3.5 text-xs text-emerald-900 border border-emerald-200 leading-relaxed">
                            {{ $detail['return_note'] }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- 4. Foto Bukti Serah Terima --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-[#6F4E37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Bukti Foto Serah Terima Real-Time
                    </div>
                    <span class="text-[11px] text-gray-400">Webcam / Tangkapan Kamera</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Foto Peminjaman --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-700 block mb-2 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Bukti Saat Peminjaman
                        </span>

                        @if($detail['borrowing_evidence_url'])
                            <div
                                class="relative aspect-video rounded-xl overflow-hidden bg-gray-950 border border-gray-200 shadow-sm cursor-pointer group"
                                @click="previewImage = '{{ $detail['borrowing_evidence_url'] }}'"
                            >
                                <img
                                    src="{{ $detail['borrowing_evidence_url'] }}"
                                    alt="Foto Bukti Peminjaman"
                                    class="w-full h-full object-cover transition group-hover:scale-105"
                                >
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                    Perbesar Foto
                                </div>
                            </div>
                        @else
                            <div class="aspect-video rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-xs p-4 text-center">
                                <svg class="w-7 h-7 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Tidak ada foto bukti serah terima peminjaman</span>
                            </div>
                        @endif
                    </div>

                    {{-- Foto Pengembalian --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-700 block mb-2 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $detail['return_evidence_url'] ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                            Bukti Saat Pengembalian
                        </span>

                        @if($detail['return_evidence_url'])
                            <div
                                class="relative aspect-video rounded-xl overflow-hidden bg-gray-950 border border-gray-200 shadow-sm cursor-pointer group"
                                @click="previewImage = '{{ $detail['return_evidence_url'] }}'"
                            >
                                <img
                                    src="{{ $detail['return_evidence_url'] }}"
                                    alt="Foto Bukti Pengembalian"
                                    class="w-full h-full object-cover transition group-hover:scale-105"
                                >
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                    Perbesar Foto
                                </div>
                            </div>
                        @else
                            <div class="aspect-video rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-xs p-4 text-center">
                                <svg class="w-7 h-7 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $detail['dates']['returned_at'] ? 'Tidak ada foto bukti pengembalian' : 'Barang belum dikembalikan' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Lightbox Zoom Foto Bukti --}}
    <div
        x-show="previewImage !== null"
        class="fixed inset-0 z-60 bg-black/90 flex items-center justify-center p-4"
        x-cloak
        @click="previewImage = null"
    >
        <div class="relative max-w-4xl max-h-[90vh]">
            <img :src="previewImage" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl border border-white/20" alt="Preview Foto Bukti">
            <button
                type="button"
                @click="previewImage = null"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 font-bold text-sm bg-white/20 px-3 py-1 rounded-lg backdrop-blur-md"
            >
                ✕ Tutup Gambar
            </button>
        </div>
    </div>

</div>

@endsection
