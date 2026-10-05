@extends('layouts.app')

@section('title', 'Form Pengajuan Peminjaman | SITEFA')

@section('content')
    {{-- Flatpickr Stylesheet & Vintage Brown Theme --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(74, 48, 34, 0.12), 0 10px 10px -5px rgba(74, 48, 34, 0.06) !important;
            border: 1px solid #E7E0DA !important;
            overflow: hidden;
            font-family: inherit !important;
        }
        .flatpickr-months {
            background: #FAF7F4 !important;
            padding-top: 0.6rem !important;
        }
        .flatpickr-current-month {
            font-weight: 700 !important;
            color: #4A3022 !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, 
        .flatpickr-day.selected:focus, 
        .flatpickr-day.selected:hover, 
        .flatpickr-day.selected.prevMonthDay, 
        .flatpickr-day.selected.nextMonthDay {
            background: #6F4E37 !important;
            border-color: #6F4E37 !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
        }
        .flatpickr-day:hover {
            background: #F4EBE4 !important;
        }
        .flatpickr-day.today {
            border-color: #C89B3C !important;
        }
        .flatpickr-day.today:hover {
            background: #F4EBE4 !important;
        }
        .flatpickr-time {
            border-top: 1px solid #F0E8E1 !important;
            background: #FAF7F4 !important;
            padding: 6px 0 !important;
        }
        .flatpickr-time input:hover, 
        .flatpickr-time .flatpickr-am-pm:hover, 
        .flatpickr-time input:focus, 
        .flatpickr-time .flatpickr-am-pm:focus {
            background: #EDE4DC !important;
        }

        /* Dark mode Flatpickr overrides */
        html.dark .flatpickr-calendar {
            background: #131B2A !important;
            border-color: #1E293B !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
            color: #F1F5F9 !important;
        }
        html.dark .flatpickr-months {
            background: #0E1420 !important;
        }
        html.dark .flatpickr-current-month,
        html.dark .flatpickr-monthDropdown-months,
        html.dark .numInputWrapper span {
            color: #F1F5F9 !important;
        }
        html.dark span.flatpickr-weekday {
            color: #94A3B8 !important;
        }
        html.dark .flatpickr-day {
            color: #E2E8F0 !important;
        }
        html.dark .flatpickr-day:hover {
            background: #1E293B !important;
        }
        html.dark .flatpickr-day.selected,
        html.dark .flatpickr-day.startRange,
        html.dark .flatpickr-day.endRange {
            background: #06B6D4 !important;
            border-color: #06B6D4 !important;
            color: #0B0F17 !important;
            font-weight: 700 !important;
        }
        html.dark .flatpickr-time {
            background: #0E1420 !important;
            border-top-color: #1E293B !important;
        }
        html.dark .flatpickr-time input,
        html.dark .flatpickr-time .flatpickr-am-pm {
            color: #F1F5F9 !important;
        }
        html.dark .flatpickr-time input:hover,
        html.dark .flatpickr-time .flatpickr-am-pm:hover,
        html.dark .flatpickr-time input:focus,
        html.dark .flatpickr-time .flatpickr-am-pm:focus {
            background: #1E293B !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <div class="max-w-3xl mx-auto px-6 py-8 page-enter">

        <div class="mb-8">
            <a
                href="{{ route('assets.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 dark:text-stone-400 hover:text-stone-800 dark:hover:text-stone-200 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Katalog
            </a>

            <h1 class="mt-4 text-3xl font-bold font-heading text-stone-800 dark:text-stone-100">
                Pengajuan Peminjaman Aset
            </h1>

            <p class="mt-1 text-stone-500 dark:text-stone-400">
                Isi estimasi pengembalian dan keperluan pinjam untuk ditinjau oleh Admin TEFA.
            </p>
        </div>

        @if (session('wa_confirmation_url'))
            <div class="mb-6 rounded-2xl bg-[#FAF7F4] dark:bg-[#1A1412] border-2 border-[#6F4E37]/30 dark:border-amber-600/40 p-5 shadow-sm text-stone-800 dark:text-amber-100 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-xl bg-[#25D366]/10 text-[#25D366] shrink-0 mt-0.5">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#4A3022] dark:text-amber-200">
                            {{ session('success') ?? 'Permohonan Peminjaman Berhasil Diajukan!' }}
                        </h4>
                        <p class="text-xs text-stone-600 dark:text-stone-300 mt-1 leading-relaxed">
                            silahkan konfirmasi ulang melalui nomor wa admin TEFA
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center">
                    <a
                        href="{{ session('wa_confirmation_url') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#20ba59] active:scale-95 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all duration-200"
                    >
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Konfirmasi via WhatsApp Admin</span>
                    </a>
                </div>
            </div>
        @elseif (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-500/30 p-4 text-sm font-medium text-emerald-800 dark:text-neon-emerald flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 dark:text-neon-emerald shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-stone-200/70 dark:border-stone-800/80 bg-white/95 dark:bg-[#131B2A]/90 backdrop-blur-md shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)]">

            {{-- Informasi Aset yang Dipilih --}}
            <div class="border-b border-stone-200/70 dark:border-stone-800 bg-gradient-to-r from-amber-50/60 to-orange-50/40 dark:from-stone-900/80 dark:to-[#131B2A]/80 p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    <div class="h-36 w-full overflow-hidden rounded-xl bg-stone-100 dark:bg-stone-800 sm:w-48 shrink-0 border border-stone-200 dark:border-stone-700 shadow-sm relative aspect-[4/3]">
                        @if ($asset->photo_url)
                            <img
                                src="{{ $asset->photo_url }}"
                                alt="{{ $asset->name }}"
                                width="192"
                                height="144"
                                loading="lazy"
                                decoding="async"
                                onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                class="h-full w-full object-cover aspect-[4/3]"
                            >
                            <div class="hidden w-full h-full min-h-[140px] bg-stone-900/60 border border-stone-800 rounded-xl flex flex-col items-center justify-center p-4 text-center">
                                <svg class="w-8 h-8 text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-xs text-stone-500 font-medium">Barang belum memiliki foto</span>
                            </div>
                        @else
                            <div class="w-full h-full min-h-[140px] bg-stone-900/60 border border-stone-800 rounded-xl flex flex-col items-center justify-center p-4 text-center">
                                <svg class="w-8 h-8 text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-xs text-stone-500 font-medium">Barang belum memiliki foto</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if ($asset->category)
                            <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-700 dark:text-neon-glowamber bg-amber-100/80 dark:bg-amber-950/50 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-500/30 mb-1.5">
                                {{ $asset->category->name }}
                            </span>
                        @endif

                        <h2 class="text-xl font-bold text-stone-800 dark:text-stone-100">
                            {{ $asset->name }}
                        </h2>

                        <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-stone-600 dark:text-stone-300">
                            <div><span class="text-stone-400 dark:text-stone-500">Kode Aset:</span> <span class="font-mono font-semibold text-stone-800 dark:text-stone-200">{{ $asset->asset_code }}</span></div>
                            @if ($asset->brand)
                                <div><span class="text-stone-400 dark:text-stone-500">Merk:</span> <span class="font-medium text-stone-800 dark:text-stone-200">{{ $asset->brand }}</span></div>
                            @endif
                            @if ($asset->model)
                                <div><span class="text-stone-400 dark:text-stone-500">Model:</span> <span class="font-medium text-stone-800 dark:text-stone-200">{{ $asset->model }}</span></div>
                            @endif
                            @if ($asset->serial_number)
                                <div><span class="text-stone-400 dark:text-stone-500">Serial No:</span> <span class="font-mono text-stone-800 dark:text-stone-200">{{ $asset->serial_number }}</span></div>
                            @endif
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-neon-emerald border border-emerald-200 dark:border-emerald-500/30">
                                ● Status: Siap Dipinjam
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Form Permohonan Peminjaman --}}
            <form
                method="POST"
                action="{{ route('assets.borrow.store', $asset) }}"
                class="space-y-6 p-6 sm:p-8"
                x-data="borrowFormHandler()"
            >
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">

                {{-- Alert Info Alur Peminjaman & Batas Waktu 10 Menit --}}
                <div class="rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-500/40 p-4 flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-neon-glowamber shrink-0 mt-0.5">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-xs text-stone-700 dark:text-stone-300 leading-relaxed">
                        <span class="font-bold text-amber-950 dark:text-neon-glowamber">Aturan Serah Terima 10 Menit:</span>
                        Setelah permohonan berhasil dikirim, Anda memiliki batas waktu <strong>10 menit</strong> untuk segera datang ke <strong>Ruang RPL</strong> dan menemui <strong>Mas Donny</strong>. Jika dalam 10 menit belum hadir, pemesanan otomatis hangus dan unit kembali ke katalog.
                    </div>
                </div>

                {{-- Tujuan Kebutuhan Peminjaman --}}
                <div>
                    <label class="block text-sm font-semibold text-stone-800 dark:text-stone-100 mb-1.5">
                        Tujuan Kebutuhan <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-xs text-stone-500 dark:text-stone-400 mb-3">
                        Pilih peruntukan peminjaman aset inventaris ini.
                    </p>

                    @php
                        $isGuru = auth()->user()?->hasRole('guru');
                        $isSiswa = auth()->user()?->hasRole('siswa');
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- KBM / Mengajar --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="purposeCategory === 'mengajar' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                            @if(! $isGuru) style="opacity: 0.6; cursor: not-allowed;" @endif
                        >
                            <input
                                type="radio"
                                name="purpose_category"
                                value="mengajar"
                                x-model="purposeCategory"
                                @if(! $isGuru) disabled @endif
                                class="mt-0.5 text-[#6F4E37] focus:ring-[#6F4E37] dark:focus:ring-amber-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-bold text-stone-800 dark:text-stone-100">KBM / Mengajar di Kelas</span>
                                    @if($isGuru)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Prioritas Guru KBM
                                        </span>
                                    @else
                                        <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded bg-stone-100 text-stone-500 dark:bg-stone-800 dark:text-stone-400">
                                            Khusus Guru
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Digunakan langsung untuk kegiatan pembelajaran di ruang kelas/lab.</p>
                            </div>
                        </label>

                        {{-- Praktik TEFA --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="purposeCategory === 'praktik' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                        >
                            <input
                                type="radio"
                                name="purpose_category"
                                value="praktik"
                                x-model="purposeCategory"
                                class="mt-0.5 text-[#6F4E37] focus:ring-[#6F4E37] dark:focus:ring-amber-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <span class="text-xs font-bold text-stone-800 dark:text-stone-100">Praktik</span>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Digunakan untuk praktikum seperti biasa.</p>
                            </div>
                        </label>

                        {{-- Ujian / Penilaian --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="purposeCategory === 'ujian' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                        >
                            <input
                                type="radio"
                                name="purpose_category"
                                value="ujian"
                                x-model="purposeCategory"
                                class="mt-0.5 text-[#6F4E37] focus:ring-[#6F4E37] dark:focus:ring-amber-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <span class="text-xs font-bold text-stone-800 dark:text-stone-100">Ujian / Penilaian Kompetensi</span>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Pelaksanaan ujian asesmen, sertifikasi, atau presentasi akhir.</p>
                            </div>
                        </label>

                        {{-- Lainnya --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="purposeCategory === 'lainnya' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                        >
                            <input
                                type="radio"
                                name="purpose_category"
                                value="lainnya"
                                x-model="purposeCategory"
                                class="mt-0.5 text-[#6F4E37] focus:ring-[#6F4E37] dark:focus:ring-amber-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <span class="text-xs font-bold text-stone-800 dark:text-stone-100">Keperluan Lainnya</span>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Penugasan khusus dari pembimbing atau keperluan administratif.</p>
                            </div>
                        </label>
                    </div>

                    @error('purpose_category')
                        <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tingkat Urgensi --}}
                <div>
                    <label class="block text-sm font-semibold text-stone-800 dark:text-stone-100 mb-1.5">
                        Tingkat Urgensi <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-xs text-stone-500 dark:text-stone-400 mb-3">
                        Pilih tingkat prioritas kebutuhan Anda. Jika memilih mendesak, alasan wajib diuraikan pada catatan.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Biasa --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="urgencyLevel === 'biasa' ? 'bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                        >
                            <input
                                type="radio"
                                name="urgency_level"
                                value="biasa"
                                x-model="urgencyLevel"
                                class="mt-0.5 text-[#6F4E37] focus:ring-[#6F4E37] dark:focus:ring-amber-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-stone-800 dark:text-stone-100">Biasa (Reguler)</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-400">Standar</span>
                                </div>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Jadwal peminjaman normal tanpa kendala waktu mendadak.</p>
                            </div>
                        </label>

                        {{-- Mendesak --}}
                        <label
                            class="relative flex items-start p-3.5 rounded-xl border transition cursor-pointer"
                            :class="urgencyLevel === 'mendesak' ? 'bg-rose-50/80 border-rose-500 dark:bg-rose-950/40 dark:border-rose-500 shadow-xs' : 'bg-white dark:bg-[#0E1420] border-stone-200 dark:border-stone-800 hover:border-stone-300'"
                        >
                            <input
                                type="radio"
                                name="urgency_level"
                                value="mendesak"
                                x-model="urgencyLevel"
                                class="mt-0.5 text-rose-600 focus:ring-rose-500 border-stone-300 dark:border-stone-700"
                            >
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-rose-800 dark:text-rose-300">Mendesak / Urgent</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-300">Prioritas Antrean</span>
                                </div>
                                <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">Kebutuhan darurat / mendadak. <em>Wajib isi alasan di catatan</em>.</p>
                            </div>
                        </label>
                    </div>

                    <div x-show="urgencyLevel === 'mendesak'" x-cloak class="mt-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 p-2.5 text-xs text-rose-800 dark:text-rose-300 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Urgensi mendesak dipilih: Anda <strong>wajib</strong> mengisi uraian alasan pada kolom Catatan Keperluan di bawah ini.</span>
                    </div>

                    @error('urgency_level')
                        <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Rencana Pengembalian (Modern Flatpickr & Presets) --}}
                <div>
                    @if($isSiswa)
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-stone-800 dark:text-stone-100">
                                Tenggat Waktu Pengembalian <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-semibold text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 px-2 py-0.5 rounded-md flex items-center gap-1">
                                <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Terkunci Otomatis (Siswa)
                            </span>
                        </div>

                        {{-- Tampilan Informasi Terkunci Siswa (Read-Only) --}}
                        <div class="rounded-xl border border-amber-200/90 dark:border-amber-500/30 bg-amber-50/70 dark:bg-amber-950/20 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-start sm:items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-neon-glowamber flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-700/50 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-stone-900 dark:text-stone-100 flex items-center gap-2">
                                        <span>Batas Pengembalian: Hari ini pukul 15:15 WIB</span>
                                    </div>
                                    <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                                        Sesuai regulasi lab TEFA, peminjaman siswa wajib dikembalikan pada hari yang sama sebelum jam kepulangan sekolah.
                                    </p>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-200/70 dark:bg-amber-900/60 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-600/50 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Read-Only
                            </span>
                        </div>
                        <input type="hidden" name="due_at" value="{{ now()->setTime(15, 15, 0)->format('Y-m-d H:i') }}">
                    @else
                        <div class="flex items-center justify-between mb-1">
                            <label
                                for="due_at_picker"
                                class="block text-sm font-semibold text-stone-800 dark:text-stone-100"
                            >
                                Tenggat Waktu Pengembalian <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-xs text-amber-800 dark:text-neon-glowamber bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-500/30 px-2 py-0.5 rounded-md font-medium">
                                Fleksibel & Otomatis
                            </span>
                        </div>

                        <p class="text-xs text-stone-500 dark:text-stone-400 mb-2.5">
                            Pilih batas waktu pengembalian barang. Gunakan tombol pilihan cepat atau tentukan tanggal & waktu pada kalender.
                        </p>

                        {{-- Tombol Preset Cepat Guru --}}
                        <div class="mb-2.5 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-stone-500 dark:text-stone-400 font-medium mr-1">Pilihan Cepat:</span>
                            <button
                                type="button"
                                @click="setDuePreset(1)"
                                :class="selectedPreset === 1 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm dark:bg-amber-600 dark:text-white dark:border-amber-500 dark:shadow-neon-amber' : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200 dark:bg-[#0E1420] dark:text-stone-300 dark:border-stone-800 dark:hover:border-stone-700'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>H+1</span>
                            </button>
                            <button
                                type="button"
                                @click="setDuePreset(3)"
                                :class="selectedPreset === 3 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm dark:bg-amber-600 dark:text-white dark:border-amber-500 dark:shadow-neon-amber' : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200 dark:bg-[#0E1420] dark:text-stone-300 dark:border-stone-800 dark:hover:border-stone-700'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>H+3 (Standar)</span>
                            </button>
                            <button
                                type="button"
                                @click="setDuePreset(7)"
                                :class="selectedPreset === 7 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm dark:bg-amber-600 dark:text-white dark:border-amber-500 dark:shadow-neon-amber' : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200 dark:bg-[#0E1420] dark:text-stone-300 dark:border-stone-800 dark:hover:border-stone-700'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>H+7 (1 Minggu)</span>
                            </button>
                            <button
                                type="button"
                                @click="setDuePreset(30)"
                                :class="selectedPreset === 30 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm dark:bg-amber-600 dark:text-white dark:border-amber-500 dark:shadow-neon-amber' : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200 dark:bg-[#0E1420] dark:text-stone-300 dark:border-stone-800 dark:hover:border-stone-700'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>1 Bulan (H+30 hari)</span>
                            </button>
                        </div>

                        <div class="relative">
                            <input
                                type="text"
                                id="due_at_picker"
                                name="due_at"
                                x-ref="dueAtInput"
                                value="{{ old('due_at') }}"
                                placeholder="Pilih tanggal dan jam pengembalian..."
                                readonly
                                required
                                class="w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] px-4 py-2.5 text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 shadow-sm focus:border-[#6F4E37] dark:focus:border-cyan-500 focus:bg-white dark:focus:bg-[#0B0F17] focus:ring-2 focus:ring-[#6F4E37]/20 dark:focus:ring-cyan-500/20 transition cursor-pointer"
                            >
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-stone-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        @error('due_at')
                            <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">
                                {{ $message }}
                            </p>
                        @enderror
                    @endif
                </div>

                {{-- Catatan / Keperluan Pinjam --}}
                <div>
                    <label
                        for="borrower_note"
                        class="block text-sm font-semibold text-stone-800 dark:text-stone-100 mb-1"
                    >
                        Keperluan Peminjaman
                        <span x-show="urgencyLevel === 'mendesak'" class="text-rose-600 font-bold ml-1">* (Wajib diisi untuk urgensi mendesak)</span>
                        <span x-show="urgencyLevel !== 'mendesak'" class="font-normal text-stone-400 dark:text-stone-500 ml-1">(opsional)</span>
                    </label>
                    <textarea
                        id="borrower_note"
                        name="borrower_note"
                        rows="3"
                        :required="urgencyLevel === 'mendesak'"
                        placeholder="Contoh: Digunakan untuk pengerjaan project praktikum TEFA di Lab Pemrograman..."
                        class="w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] px-4 py-2.5 text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 shadow-sm focus:border-[#6F4E37] dark:focus:border-cyan-500 focus:bg-white dark:focus:bg-[#0B0F17] focus:ring-2 focus:ring-[#6F4E37]/20 dark:focus:ring-cyan-500/20 transition"
                    >{{ old('borrower_note') }}</textarea>
                    @error('borrower_note')
                        <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <a
                        href="{{ route('assets.index') }}"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 font-semibold text-sm transition text-center shadow-xs cursor-pointer interactive-btn"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-7 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-bold text-sm shadow-md transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 cursor-pointer interactive-btn"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Kirim Permohonan Peminjaman</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script>
        function borrowFormHandler() {
            return {
                fpInstance: null,
                selectedPreset: 3,
                purposeCategory: '{{ old('purpose_category', auth()->user()?->hasRole('guru') ? 'mengajar' : 'praktik') }}',
                urgencyLevel: '{{ old('urgency_level', 'biasa') }}',

                init() {
                    const defaultDue = new Date();
                    defaultDue.setDate(defaultDue.getDate() + 3);
                    defaultDue.setHours(17, 0, 0, 0);

                    if (this.$refs.dueAtInput && typeof flatpickr !== 'undefined') {
                        this.fpInstance = flatpickr(this.$refs.dueAtInput, {
                            locale: "id",
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            altInput: true,
                            altFormat: "d F Y, H:i",
                            minDate: "today",
                            defaultDate: defaultDue,
                            time_24hr: true,
                            onChange: (selectedDates) => {
                                if (selectedDates.length > 0) {
                                    const sel = selectedDates[0];
                                    const now = new Date();
                                    const diffDays = Math.round((sel.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
                                    if (![1, 3, 7, 30].includes(diffDays)) {
                                        this.selectedPreset = null;
                                    }
                                }
                            }
                        });
                    } else if (this.$refs.dueAtInput) {
                        this.formatFallbackInput(defaultDue);
                    }
                },

                setDuePreset(days) {
                    this.selectedPreset = days;
                    const targetDate = new Date();
                    targetDate.setDate(targetDate.getDate() + days);
                    targetDate.setHours(17, 0, 0, 0);

                    if (this.fpInstance) {
                        this.fpInstance.setDate(targetDate, true);
                    } else {
                        this.formatFallbackInput(targetDate);
                    }
                },

                formatFallbackInput(d) {
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    const hh = String(d.getHours()).padStart(2, '0');
                    const ii = String(d.getMinutes()).padStart(2, '0');
                    const formatted = `${yyyy}-${mm}-${dd} ${hh}:${ii}`;
                    if (this.$refs.dueAtInput) {
                        this.$refs.dueAtInput.value = formatted;
                    }
                    const el = document.getElementById('due_at_picker');
                    if (el) {
                        el.value = formatted;
                    }
                }
            }
        }
    </script>
@endsection
