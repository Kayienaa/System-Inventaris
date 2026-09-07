@extends('layouts.app')

@section('title', 'Detail Transaksi Peminjaman ' . $detail['transaction_code'] . ' | SITEFA')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8" x-data="{ previewImage: null, rejectModalOpen: false }">

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
                @elseif($detail['raw_status'] === 'pending')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">
                        ● Menunggu Persetujuan
                    </span>
                @elseif($detail['raw_status'] === 'approved')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                        ✓ Disetujui (Siap Ambil)
                    </span>
                @elseif($detail['raw_status'] === 'borrowed')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        ● Sedang Dipinjam
                    </span>
                @elseif($detail['raw_status'] === 'return_pending_verification')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">
                        ● Menunggu Verifikasi Pengembalian
                    </span>
                @elseif($detail['raw_status'] === 'returned')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        ✓ Selesai (Kembali)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300">
                        {{ ucfirst($detail['status']) }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if(!empty($detail['wa_url']))
                    <a
                        href="{{ $detail['wa_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm active:scale-95"
                        title="Kirim Pengingat WhatsApp ke Peminjam"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Kirim WhatsApp
                    </a>
                @elseif($detail['is_overdue'] || $detail['raw_status'] === 'borrowed')
                    <button
                        type="button"
                        disabled
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed border border-gray-200 opacity-60"
                        title="Nomor WhatsApp peminjam belum terdaftar di profil"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Kirim WhatsApp
                    </button>
                @endif
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

            {{-- Action Box 1: Persetujuan Request --}}
            @if($detail['raw_status'] === 'pending')
                <div class="bg-yellow-50/90 border border-yellow-200 rounded-2xl p-5">
                    <div class="flex items-center gap-2 text-xs font-bold text-yellow-900 uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Tinjau Permohonan Peminjaman
                    </div>
                    <p class="text-xs text-yellow-800 leading-relaxed mb-4">
                        Permohonan ini menunggu persetujuan Admin TEFA. Klik <strong>Setujui Permintaan</strong> agar peminjam dapat mengambil unit fisik di ruangan TEFA.
                    </p>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <form action="{{ route('admin.borrowings.approve', $borrowing) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui Permintaan
                            </button>
                        </form>

                        <button
                            type="button"
                            @click="rejectModalOpen = !rejectModalOpen"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak Permintaan
                        </button>
                    </div>

                    <div x-show="rejectModalOpen" class="mt-4 pt-3 border-t border-yellow-200" x-cloak>
                        <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-rose-900 mb-1">Alasan Penolakan:</label>
                                <input
                                    type="text"
                                    name="rejection_reason"
                                    required
                                    placeholder="Contoh: Unit sedang disiapkan untuk kegiatan TEFA..."
                                    class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 text-gray-900"
                                >
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="rejectModalOpen = false" class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs">Batal</button>
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-xs">Konfirmasi Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Action Box 2: Verifikasi Pengembalian --}}
            @if($detail['raw_status'] === 'return_pending_verification')
                <div class="bg-purple-50/90 border border-purple-200 rounded-2xl p-5">
                    <div class="flex items-center gap-2 text-xs font-bold text-purple-900 uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Verifikasi Pengembalian Fisik Unit
                    </div>
                    <p class="text-xs text-purple-800 leading-relaxed mb-4">
                        Peminjam telah mengajukan pengembalian beserta foto bukti. Periksa kondisi unit fisik lalu konfirmasi verifikasi.
                    </p>

                    <form action="{{ route('admin.borrowings.verify-return', $borrowing) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Fisik Saat Kembali:</label>
                                <select name="return_condition" class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2">
                                    <option value="Baik">Baik (Normal)</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat (Masuk Perbaikan)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Admin / Verifikator:</label>
                                <input
                                    type="text"
                                    name="return_verification_note"
                                    placeholder="Contoh: Unit lengkap dalam kondisi baik..."
                                    class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2"
                                >
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verifikasi Pengembalian & Selesaikan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

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
                            <span class="text-gray-700 font-mono">{{ $detail['borrower']['formatted_phone'] ?? \App\Services\WhatsAppNotificationService::formatDisplayPhoneNumber($detail['borrower']['phone']) }}</span>
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
                    Timeline & Jejak Persetujuan
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">Diajukan</span>
                        <span class="font-bold text-gray-800 text-xs">{{ $detail['dates']['requested_at'] }}</span>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">Diserahterimakan</span>
                        <span class="font-bold text-gray-800 text-xs">{{ $detail['dates']['borrowed_at'] }}</span>
                    </div>

                    <div class="p-3 rounded-xl border {{ $detail['is_overdue'] ? 'bg-rose-50 border-rose-200' : 'bg-amber-50/60 border-amber-200' }}">
                        <span class="text-[10px] block mb-1 {{ $detail['is_overdue'] ? 'text-rose-600 font-bold' : 'text-amber-800 font-semibold' }}">
                            Target Kembali
                        </span>
                        <span class="font-bold text-xs {{ $detail['is_overdue'] ? 'text-rose-700' : 'text-amber-900' }}">
                            {{ $detail['dates']['due_at'] }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl border {{ $detail['dates']['returned_at'] ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-100' }}">
                        <span class="text-[10px] text-gray-400 block mb-1">Waktu Kembali Fisik</span>
                        <span class="font-bold text-xs {{ $detail['dates']['returned_at'] ? 'text-emerald-800' : 'text-gray-400 italic' }}">
                            {{ $detail['dates']['returned_at'] ?? 'Belum' }}
                        </span>
                    </div>
                </div>

                @if(!empty($detail['approved_by']) || !empty($detail['return_verified_by']))
                    <div class="mt-3 pt-3 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @if(!empty($detail['approved_by']))
                            <div class="text-stone-600">
                                <span class="text-gray-400 text-[11px]">Disetujui Oleh:</span>
                                <span class="font-semibold text-gray-800 ml-1">{{ $detail['approved_by'] }}</span>
                            </div>
                        @endif
                        @if(!empty($detail['return_verified_by']))
                            <div class="text-stone-600">
                                <span class="text-gray-400 text-[11px]">Diverifikasi Oleh:</span>
                                <span class="font-semibold text-gray-800 ml-1">{{ $detail['return_verified_by'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- 3. Catatan --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-[#6F4E37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Catatan Keperluan
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
                            Bukti Saat Serah Terima
                        </span>

                        @if($detail['borrowing_evidence_url'])
                            <div
                                class="relative aspect-video rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-gray-200 shadow-sm cursor-pointer group"
                                @click="previewImage = '{{ $detail['borrowing_evidence_url'] }}'"
                            >
                                <img
                                    src="{{ $detail['borrowing_evidence_url'] }}"
                                    alt="Foto Bukti Peminjaman"
                                    width="480"
                                    height="270"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                >
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                    Perbesar Foto
                                </div>
                            </div>
                        @else
                            <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-stone-400 text-xs p-4 text-center">
                                <svg class="w-7 h-7 text-stone-300 dark:text-stone-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="relative aspect-video rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 border border-gray-200 shadow-sm cursor-pointer group"
                                @click="previewImage = '{{ $detail['return_evidence_url'] }}'"
                            >
                                <img
                                    src="{{ $detail['return_evidence_url'] }}"
                                    alt="Foto Bukti Pengembalian"
                                    width="480"
                                    height="270"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover aspect-video transition group-hover:scale-105"
                                >
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                    Perbesar Foto
                                </div>
                            </div>
                        @else
                            <div class="aspect-video rounded-xl bg-stone-100 dark:bg-stone-800 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-stone-400 text-xs p-4 text-center">
                                <svg class="w-7 h-7 text-stone-300 dark:text-stone-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        class="fixed inset-0 z-[70] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="previewImage = null"
        @click="previewImage = null"
    >
        <div class="relative z-[80] max-w-4xl max-h-[90vh]" @click.stop>
            <img :src="previewImage" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl border border-white/20" alt="Preview Foto Bukti">
            <button
                type="button"
                @click="previewImage = null"
                class="absolute -top-10 right-0 text-white hover:text-white font-bold text-sm bg-black/50 hover:bg-black/75 px-3 py-1 rounded-lg backdrop-blur-md transition shadow-md cursor-pointer border border-white/20"
            >
                ✕ Tutup Gambar
            </button>
        </div>
    </div>

</div>

@endsection
