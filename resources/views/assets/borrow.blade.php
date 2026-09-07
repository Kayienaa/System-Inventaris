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
                Pengajuan Peminjaman Aset
            </h1>

            <p class="mt-1 text-stone-500 dark:text-stone-400">
                Isi estimasi pengembalian dan keperluan pinjam untuk ditinjau oleh Admin TEFA.
            </p>
        </div>

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

                {{-- Alert Info Alur Peminjaman Dua Tahap --}}
                <div class="rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-500/30 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-700 dark:text-neon-glowamber shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs text-stone-700 dark:text-stone-300 leading-relaxed">
                        <span class="font-bold text-amber-900 dark:text-neon-glowamber">Alur Peminjaman Dua Tahap:</span> Setelah permohonan diajukan dan disetujui oleh Admin TEFA, Anda dapat mengambil unit fisik di ruangan TEFA dengan melakukan foto serah terima bersama Admin.
                    </div>
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
                </div>

                {{-- Catatan / Keperluan Pinjam --}}
                <div>
                    <label
                        for="borrower_note"
                        class="block text-sm font-semibold text-stone-800 dark:text-stone-100 mb-1"
                    >
                        Keperluan Peminjaman <span class="font-normal text-stone-400 dark:text-stone-500">(opsional)</span>
                    </label>
                    <textarea
                        id="borrower_note"
                        name="borrower_note"
                        rows="3"
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
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 font-semibold text-sm transition text-center shadow-xs cursor-pointer"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-7 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-bold text-sm shadow-md transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
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

                init() {
                    const defaultDue = new Date();
                    defaultDue.setDate(defaultDue.getDate() + 3);
                    defaultDue.setHours(17, 0, 0, 0);

                    if (typeof flatpickr !== 'undefined') {
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
                                    if (![1, 3, 7].includes(diffDays)) {
                                        this.selectedPreset = null;
                                    }
                                }
                            }
                        });
                    } else {
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
