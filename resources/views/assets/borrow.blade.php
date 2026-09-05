@extends('layouts.app')

@section('title', 'Form Peminjaman Barang | TE-Vault')

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

    <div class="max-w-3xl mx-auto px-6 py-8">

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

            <h1 class="mt-4 text-3xl font-bold text-stone-800 dark:text-stone-100">
                Peminjaman Aset (Instant Borrow)
            </h1>

            <p class="mt-1 text-stone-500 dark:text-stone-400">
                Ambil foto serah terima secara real-time via kamera untuk langsung memproses peminjaman.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-stone-200/70 dark:border-stone-800/80 bg-white/95 dark:bg-[#131B2A]/90 backdrop-blur-md shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)]">

            {{-- Informasi Aset yang Dipilih --}}
            <div class="border-b border-stone-200/70 dark:border-stone-800 bg-gradient-to-r from-amber-50/60 to-orange-50/40 dark:from-stone-900/80 dark:to-[#131B2A]/80 p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    <div class="h-36 w-full overflow-hidden rounded-xl bg-stone-100 dark:bg-stone-800 sm:w-48 shrink-0 border border-stone-200 dark:border-stone-700 shadow-sm relative aspect-[4/3]">
                        @php
                            $photoExists = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                        @endphp

                        @if ($photoExists)
                            <img
                                src="{{ asset('storage/' . $asset->photo_path) }}"
                                alt="{{ $asset->name }}"
                                width="192"
                                height="144"
                                loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover aspect-[4/3]"
                            >
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center text-xs text-stone-400 dark:text-stone-300 bg-stone-100 dark:bg-stone-800">
                                <svg class="w-8 h-8 text-stone-300 dark:text-stone-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Foto tidak tersedia
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

            {{-- Form Peminjaman Langsung --}}
            <form
                method="POST"
                action="{{ route('assets.borrow.store', $asset) }}"
                enctype="multipart/form-data"
                class="space-y-6 p-6 sm:p-8"
                x-data="borrowWebcamHandler()"
                @submit="onFormSubmit($event)"
            >
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">

                {{-- Alert Info Instant Borrow --}}
                <div class="rounded-xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-500/30 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-neon-emerald shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs text-emerald-800 dark:text-stone-200 leading-relaxed">
                        <span class="font-bold text-emerald-900 dark:text-neon-emerald">Instant Borrowing Active:</span> Setelah disubmit dengan foto bukti, peminjaman langsung aktif dengan status <strong>Dipinjam</strong> dan tenggat otomatis <strong>H+3</strong>.
                    </div>
                </div>

                {{-- Modul Kamera Real-time Webcam --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-stone-800 dark:text-stone-100">
                            Foto Bukti Serah Terima Real-Time <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs font-medium text-amber-800 dark:text-neon-glowamber bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-500/30 px-2 py-0.5 rounded-md flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-neon-emerald"></span>
                            Wajib Kamera Real-Time
                        </span>
                    </div>

                    <div class="rounded-2xl border-2 border-dashed border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-stone-900/50 p-4 transition hover:border-stone-400 dark:hover:border-stone-600">

                        {{-- Viewport Kamera Bersih 100% --}}
                        <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-gray-950 flex items-center justify-center shadow-inner">
                            
                            {{-- Live Video Stream --}}
                            <video
                                x-ref="videoElement"
                                autoplay
                                playsinline
                                class="h-full w-full object-cover"
                                :class="{ 'hidden': capturedPhoto || !isCameraOpen }"
                            ></video>

                            {{-- Captured Photo Preview --}}
                            <img
                                x-show="capturedPhoto"
                                :src="capturedPhoto"
                                class="h-full w-full object-cover"
                                alt="Hasil Foto Bukti"
                            >

                            {{-- Hidden Canvas for Capture --}}
                            <canvas x-ref="canvasElement" class="hidden"></canvas>

                            {{-- Placeholder jika kamera belum aktif dan belum ada foto --}}
                            <div x-show="!isCameraOpen && !capturedPhoto" class="flex flex-col items-center justify-center p-6 text-center text-gray-400">
                                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-3 text-gray-300 shadow-inner">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-white">Kamera Belum Aktif</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs">Tekan tombol "Buka Kamera" di bawah untuk mulai mengambil foto serah terima.</p>
                            </div>

                            {{-- Live Badge Overlay --}}
                            <div x-show="isCameraOpen && !capturedPhoto" class="absolute top-3 left-3 flex items-center gap-2 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full pointer-events-none z-10">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                <span class="font-medium">Live Camera</span>
                            </div>

                            {{-- Captured Success Badge --}}
                            <div x-show="capturedPhoto" class="absolute top-3 left-3 flex items-center gap-1.5 bg-emerald-600/90 text-white text-xs px-3 py-1 rounded-full shadow-md backdrop-blur-sm pointer-events-none z-10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Foto Siap Disimpan</span>
                            </div>

                        </div>

                        {{-- Bar Kontrol Khusus di Bawah Kotak Kamera --}}
                        <div class="mt-4">
                            {{-- State 1: Kamera Belum Aktif --}}
                            <div x-show="!isCameraOpen && !capturedPhoto" class="flex flex-col sm:flex-row items-center justify-between gap-3 p-2">
                                <button
                                    type="button"
                                    @click="openCamera()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber text-sm font-semibold transition-all duration-200 shadow-md active:scale-95 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Buka Kamera</span>
                                </button>
                                <p class="text-xs text-stone-500 dark:text-stone-400 text-center sm:text-right">
                                    Izinkan izin kamera pada peramban untuk melanjutkan.
                                </p>
                            </div>

                            {{-- State 2: Kamera Aktif (Live Camera Control Bar) --}}
                            <div x-show="isCameraOpen && !capturedPhoto" class="relative flex items-center justify-center py-2 px-2">
                                {{-- Tombol Lingkaran Shutter Putih Ring Ganda di Tengah --}}
                                <button
                                    type="button"
                                    @click="snapSnapshot()"
                                    class="w-14 h-14 rounded-full border-4 border-[#6F4E37] dark:border-neon-glowamber bg-white shadow-md active:scale-95 flex items-center justify-center mx-auto hover:scale-105 transition-all text-[#6F4E37] ring-4 ring-black/10 dark:ring-amber-500/20 cursor-pointer"
                                    title="Ambil Foto"
                                >
                                    <svg class="w-6 h-6 text-[#6F4E37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>

                                {{-- Tombol Flip Kamera di Samping --}}
                                <button
                                    type="button"
                                    @click="switchFacingMode()"
                                    class="absolute right-2 sm:right-6 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 text-xs font-medium transition shadow-xs active:scale-95 cursor-pointer"
                                    title="Ganti Kamera Depan/Belakang"
                                >
                                    <svg class="w-4 h-4 text-stone-600 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span class="hidden sm:inline">Ganti Kamera</span>
                                </button>
                            </div>

                            {{-- State 3: Foto Siap Disimpan (Tombol Bersih di Bawah Kanvas) --}}
                            <div x-show="capturedPhoto" class="flex flex-col sm:flex-row items-center justify-between gap-3 p-2">
                                <div class="text-xs text-emerald-700 dark:text-neon-emerald font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-neon-emerald shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Foto serah terima berhasil diambil dan bersih tanpa tertutupi tombol.</span>
                                </div>

                                <button
                                    type="button"
                                    @click="retakeSnapshot()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] hover:bg-stone-100 dark:hover:bg-stone-800 text-stone-700 dark:text-stone-300 text-xs font-semibold shadow-xs transition active:scale-95 cursor-pointer"
                                >
                                    <svg class="w-4 h-4 text-amber-600 dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>Ambil Ulang Foto</span>
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Input for Base64 Data --}}
                        <input type="hidden" name="borrowing_evidence" :value="capturedPhoto">

                    </div>

                    @error('borrowing_evidence')
                        <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tanggal Rencana Pengembalian (Modern Flatpickr & Presets) --}}
                <div>
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

                    {{-- Tombol Preset Cepat --}}
                    <div class="mb-2.5 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-stone-500 dark:text-stone-400 font-medium mr-1">Pilihan Cepat:</span>
                        <button
                            type="button"
                            @click="setDuePreset(1)"
                            :class="activePreset === 1 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm ring-2 ring-[#6F4E37]/30 dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:shadow-neon-amber dark:border-amber-500' : 'bg-stone-100 dark:bg-stone-900/80 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-800 border-stone-200 dark:border-stone-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>H+1</span>
                        </button>
                        <button
                            type="button"
                            @click="setDuePreset(3)"
                            :class="activePreset === 3 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm ring-2 ring-[#6F4E37]/30 dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:shadow-neon-amber dark:border-amber-500' : 'bg-stone-100 dark:bg-stone-900/80 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-800 border-stone-200 dark:border-stone-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <span>H+3 (Bawaan)</span>
                        </button>
                        <button
                            type="button"
                            @click="setDuePreset(5)"
                            :class="activePreset === 5 ? 'bg-[#6F4E37] text-white border-[#6F4E37] shadow-sm ring-2 ring-[#6F4E37]/30 dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:shadow-neon-amber dark:border-amber-500' : 'bg-stone-100 dark:bg-stone-900/80 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-800 border-stone-200 dark:border-stone-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>H+5</span>
                        </button>
                    </div>

                    {{-- Input Tanggal Tenggat Pengembalian (type="datetime-local" step="60") --}}
                    <div class="relative">
                        <input
                            id="due_at_picker"
                            name="due_at"
                            type="datetime-local"
                            step="60"
                            value="{{ old('due_at') ? \Carbon\Carbon::parse(old('due_at'))->format('Y-m-d\TH:i') : now()->addDays(3)->format('Y-m-d\TH:i') }}"
                            placeholder="Pilih tanggal dan waktu tenggat..."
                            class="mt-1 block w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-4 py-3 pr-11 text-sm font-medium text-stone-800 dark:text-stone-100 shadow-sm focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none cursor-pointer"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-stone-400 dark:text-stone-500">
                            <svg class="w-5 h-5 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    @error('due_at')
                        <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Catatan Peminjam --}}
                <div>
                    <label
                        for="borrower_note"
                        class="block text-sm font-semibold text-stone-800 dark:text-stone-100"
                    >
                        Catatan Keperluan
                        <span class="font-normal text-stone-400 dark:text-stone-500">(opsional)</span>
                    </label>

                    <textarea
                        id="borrower_note"
                        name="borrower_note"
                        rows="2"
                        maxlength="1000"
                        placeholder="Contoh: Praktikum Mobile App TEFA di Ruang 2..."
                        class="mt-2 block w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-4 py-3 text-sm text-stone-900 dark:text-stone-100 shadow-sm focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none"
                    >{{ old('borrower_note') }}</textarea>

                    @error('borrower_note')
                        <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="flex flex-col-reverse gap-3 pt-4 border-t border-stone-100 dark:border-stone-800 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('assets.index') }}"
                        class="rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#131B2A] px-5 py-2.5 text-center text-sm font-medium text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 transition shadow-sm"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-medium rounded-xl transition-all duration-200 px-7 py-2.5 text-sm font-bold shadow-md active:scale-[0.98] flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Pinjam Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- Alpine.js WebCam Component Logic --}}
    <script>
        function borrowWebcamHandler() {
            return {
                isCameraOpen: false,
                capturedPhoto: null,
                mediaStream: null,
                facingMode: 'environment',
                activePreset: 3,
                fpInstance: null,

                init() {
                    this.$nextTick(() => {
                        const input = document.getElementById('due_at_picker');
                        if (input && typeof flatpickr !== 'undefined') {
                            const defaultDateValue = input.value || '{{ now()->addDays(3)->format("Y-m-d\\TH:i") }}';
                            this.fpInstance = flatpickr("#due_at_picker", {
                                enableTime: true,
                                time_24hr: true,
                                dateFormat: "Y-m-d\\TH:i",
                                altInput: true,
                                altFormat: "d/m/Y H:i",
                                altInputClass: "mt-1 block w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] px-4 py-3 pr-11 text-sm font-medium text-stone-800 dark:text-stone-100 shadow-sm focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none cursor-pointer transition",
                                defaultDate: defaultDateValue,
                                minDate: "today",
                                minuteIncrement: 1,
                                onChange: (selectedDates) => {
                                    if (selectedDates && selectedDates[0]) {
                                        const now = new Date();
                                        const diffDays = Math.round((selectedDates[0] - now) / (1000 * 60 * 60 * 24));
                                        if ([1, 3, 5].includes(diffDays)) {
                                            this.activePreset = diffDays;
                                        } else {
                                            this.activePreset = null;
                                        }
                                    }
                                }
                            });
                        }
                    });
                },

                setDuePreset(days) {
                    this.activePreset = days;
                    const d = new Date();
                    d.setDate(d.getDate() + days);
                    d.setSeconds(0, 0); // Pastikan nilai detik dinolkan

                    if (this.fpInstance) {
                        this.fpInstance.setDate(d, true);
                    } else {
                        const input = document.getElementById('due_at_picker');
                        if (input) {
                            const pad = (n) => String(n).padStart(2, '0');
                            const formatted = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                            input.value = formatted;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                },

                async openCamera() {
                    try {
                        if (this.mediaStream) {
                            this.closeCamera();
                        }
                        this.mediaStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: this.facingMode,
                                width: { ideal: 1280 },
                                height: { ideal: 720 }
                            },
                            audio: false
                        });
                        this.$refs.videoElement.srcObject = this.mediaStream;
                        this.isCameraOpen = true;
                        this.capturedPhoto = null;
                    } catch (error) {
                        console.error("Camera access failed:", error);
                        alert("Tidak dapat mengakses kamera perangkat. Pastikan izin akses kamera telah diizinkan pada browser perangkat kamu.");
                    }
                },

                applyWatermark(canvas, userName) {
                    const ctx = canvas.getContext('2d');
                    const width = canvas.width;
                    const height = canvas.height;

                    const now = new Date();
                    const pad = (n) => String(n).padStart(2, '0');
                    const year = now.getFullYear();
                    const month = pad(now.getMonth() + 1);
                    const day = pad(now.getDate());
                    const hours = pad(now.getHours());
                    const minutes = pad(now.getMinutes());
                    const seconds = pad(now.getSeconds());
                    const timestampStr = `${year}-${month}-${day} ${hours}:${minutes}:${seconds} WIB`;
                    const textStr = `Peminjam: ${userName} | ${timestampStr}`;

                    const fontSize = Math.max(13, Math.floor(width / 38));
                    ctx.font = `bold ${fontSize}px ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;

                    const paddingX = 14;
                    const paddingY = 8;
                    const textMetrics = ctx.measureText(textStr);
                    const boxWidth = textMetrics.width + (paddingX * 2);
                    const boxHeight = fontSize + (paddingY * 2);

                    const x = width - boxWidth - 14;
                    const y = height - boxHeight - 14;

                    // Kontras tinggi: strip gelap pekat agar teks terbaca tajam dan tidak pecah
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
                    if (ctx.roundRect) {
                        ctx.beginPath();
                        ctx.roundRect(x, y, boxWidth, boxHeight, 6);
                        ctx.fill();
                    } else {
                        ctx.fillRect(x, y, boxWidth, boxHeight);
                    }

                    // Teks putih tajam
                    ctx.fillStyle = '#FFFFFF';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(textStr, x + paddingX, y + (boxHeight / 2));
                },

                snapSnapshot() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    if (!video || !canvas) return;

                    const maxDim = 1280;
                    let w = video.videoWidth || 640;
                    let h = video.videoHeight || 480;

                    // Batasi dimensi maksimal canvas: lebar/tinggi maksimal 1280px menjaga aspect ratio
                    if (w > maxDim || h > maxDim) {
                        if (w >= h) {
                            h = Math.round((h * maxDim) / w);
                            w = maxDim;
                        } else {
                            w = Math.round((w * maxDim) / h);
                            h = maxDim;
                        }
                    }

                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, w, h);

                    // Tambahkan Watermark Timestamp & Nama
                    this.applyWatermark(canvas, "{{ auth()->user()->name }}");

                    // Konversi Base64 dengan quality 0.68 (rentang 0.65 - 0.70) agar ukuran file terkunci 100-200 KB
                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.68);
                    this.closeCamera();
                },

                retakeSnapshot() {
                    this.capturedPhoto = null;
                    this.openCamera();
                },

                switchFacingMode() {
                    this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
                    this.openCamera();
                },

                closeCamera() {
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(t => t.stop());
                        this.mediaStream = null;
                    }
                    this.isCameraOpen = false;
                },

                onFormSubmit(e) {
                    if (!this.capturedPhoto) {
                        e.preventDefault();
                        alert('Silakan ambil foto bukti serah terima secara real-time via kamera terlebih dahulu.');
                        return;
                    }
                    // Stop camera stream before navigating
                    this.closeCamera();
                }
            }
        }
    </script>
@endsection
