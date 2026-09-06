@extends('layouts.app')

@section('title', 'Edit Aset: ' . $asset->name . ' | TE-Vault')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8" x-data="{
    photoPreview: null,
    updatePhotoPreview(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            this.photoPreview = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}">

    {{-- Breadcrumb & Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs text-stone-500 dark:text-stone-400 mb-2">
            <a href="{{ route('admin.assets.index') }}" class="hover:text-amber-700 dark:hover:text-amber-400 transition">Master Aset</a>
            <span>/</span>
            <span class="text-stone-800 dark:text-stone-200 font-semibold">Edit Aset ({{ $asset->asset_code }})</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-stone-900 dark:text-stone-100 tracking-tight">
                    Edit Informasi Aset
                </h1>
                <p class="text-stone-500 dark:text-stone-400 text-sm mt-1">
                    Perbarui data spesifikasi, nomor seri, kondisi, status ketersediaan, atau foto unit.
                </p>
            </div>
            <a
                href="{{ route('admin.assets.index') }}"
                class="px-4 py-2 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-800 text-stone-700 dark:text-stone-300 text-xs font-semibold hover:bg-stone-50 dark:hover:bg-stone-700 transition shadow-sm"
            >
                Kembali
            </a>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm shadow-sm">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Terdapat kesalahan pengisian form:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-6 md:p-8 transition-colors duration-300">
        <form action="{{ route('admin.assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Balanced Grid: 2/3 Data Formulir vs 1/3 Pratinjau Foto --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Kolom Kiri: Input Data Formulir (2/3) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Row 1: Kategori & Kode Aset --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="asset_category_id" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Kategori Aset <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="asset_category_id"
                                name="asset_category_id"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('asset_category_id') border-rose-400 dark:border-rose-500 @enderror"
                            >
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('asset_category_id', $asset->asset_category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_category_id')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="asset_code" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Kode Aset Unik <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="asset_code"
                                type="text"
                                name="asset_code"
                                value="{{ old('asset_code', $asset->asset_code) }}"
                                required
                                maxlength="50"
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm font-mono @error('asset_code') border-rose-400 dark:border-rose-500 @enderror"
                            >
                            @error('asset_code')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 2: Nama Aset & Nomor Seri --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Nama Barang / Unit <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $asset->name) }}"
                                required
                                maxlength="255"
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('name') border-rose-400 dark:border-rose-500 @enderror"
                            >
                            @error('name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="serial_number" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Nomor Seri (Serial Number) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="serial_number"
                                type="text"
                                name="serial_number"
                                value="{{ old('serial_number', $asset->serial_number) }}"
                                required
                                maxlength="100"
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm font-mono @error('serial_number') border-rose-400 dark:border-rose-500 @enderror"
                            >
                            @error('serial_number')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 3: Merk & Model --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="brand" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Merk / Brand <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="brand"
                                type="text"
                                name="brand"
                                value="{{ old('brand', $asset->brand) }}"
                                required
                                maxlength="100"
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('brand') border-rose-400 dark:border-rose-500 @enderror"
                            >
                            @error('brand')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Model / Tipe Spesifik
                            </label>
                            <input
                                id="model"
                                type="text"
                                name="model"
                                value="{{ old('model', $asset->model) }}"
                                maxlength="100"
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('model') border-rose-400 dark:border-rose-500 @enderror"
                            >
                            @error('model')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 4: Kondisi & Status Ketersediaan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="condition" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Kondisi Fisik <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="condition"
                                name="condition"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('condition') border-rose-400 dark:border-rose-500 @enderror"
                            >
                                @php
                                    $currentCond = old('condition', $asset->condition->value ?? (string) $asset->condition);
                                @endphp
                                @foreach ($conditions as $cond)
                                    <option value="{{ $cond->value }}" {{ $currentCond === $cond->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $cond->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('condition')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="availability_status" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                                Status Ketersediaan <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="availability_status"
                                name="availability_status"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('availability_status') border-rose-400 dark:border-rose-500 @enderror"
                            >
                                @php
                                    $currentStatus = old('availability_status', $asset->availability_status->value ?? (string) $asset->availability_status);
                                @endphp
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ $currentStatus === $st->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $st->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('availability_status')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 5: Catatan Tambahan --}}
                    <div>
                        <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                            Catatan / Spesifikasi Tambahan
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-cyan-500 focus:border-transparent transition-colors text-sm @error('notes') border-rose-400 dark:border-rose-500 @enderror"
                        >{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Kolom Kanan: Tinjauan Foto Fisik Aset & Upload (1/3) --}}
                <div class="lg:col-span-4 space-y-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                        Foto Fisik Aset
                    </label>

                    {{-- Wadah Upload & Preview Foto di Kolom Kanan --}}
                    <div class="w-full aspect-video rounded-xl bg-stone-100 dark:bg-stone-900/60 border border-stone-200 dark:border-stone-800 flex items-center justify-center overflow-hidden relative">
                        <template x-if="photoPreview">
                            <div class="w-full h-full relative">
                                <img :src="photoPreview" alt="Pratinjau Foto Baru" class="w-full h-full object-cover">
                                <div class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-xs py-1 px-2 rounded-lg">
                                    <p class="text-[11px] font-semibold text-white text-center">Pratinjau Foto Baru</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="!photoPreview">
                            <div class="w-full h-full">
                                @php
                                    $photoExists = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                                @endphp
                                @if($photoExists)
                                    <img src="{{ asset('storage/' . $asset->photo_path) }}" 
                                         alt="{{ $asset->name }}" 
                                         loading="lazy" 
                                         decoding="async" 
                                         onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                         class="w-full h-full object-cover rounded-lg">
                                    <div class="hidden w-full h-full min-h-[50px] bg-stone-100 dark:bg-stone-900/60 border border-stone-200 dark:border-stone-800 rounded-lg flex flex-col items-center justify-center p-2 text-center">
                                        <svg class="w-5 h-5 text-stone-400 dark:text-stone-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-[10px] text-stone-500 font-medium">Foto belum ada</span>
                                    </div>
                                @else
                                    <div class="w-full h-full min-h-[50px] bg-stone-100 dark:bg-stone-900/60 border border-stone-200 dark:border-stone-800 rounded-lg flex flex-col items-center justify-center p-2 text-center">
                                        <svg class="w-5 h-5 text-stone-400 dark:text-stone-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-[10px] text-stone-500 font-medium">Foto belum ada</span>
                                    </div>
                                @endif
                            </div>
                        </template>
                    </div>

                    {{-- Upload Foto Input --}}
                    <div class="pt-2">
                        <input
                            type="file"
                            name="photo"
                            id="photo"
                            accept="image/jpeg,image/png,image/webp,image/jpg"
                            @change="updatePhotoPreview"
                            class="block w-full text-xs text-stone-500 dark:text-stone-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 dark:file:bg-stone-800 file:text-amber-800 dark:file:text-stone-200 hover:file:bg-amber-200 dark:hover:file:bg-stone-700 cursor-pointer"
                        >
                        <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-2">
                            Pilih file gambar baru jika ingin mengganti foto saat ini (Maks 2MB, JPG/PNG/WEBP). Foto lama akan otomatis digantikan.
                        </p>
                        @error('photo')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- Form Actions --}}
            <div class="mt-8 pt-5 border-t border-stone-200 dark:border-stone-800 flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.assets.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 text-stone-700 dark:text-stone-300 text-xs font-semibold hover:bg-stone-50 dark:hover:bg-stone-800 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] dark:bg-none dark:bg-gradient-to-r dark:from-cyan-600 dark:to-teal-500 dark:hover:from-cyan-500 dark:hover:to-teal-400 text-white text-xs font-bold shadow-md shadow-[#6F4E37]/20 dark:shadow-cyan-500/20 transition-all duration-150 active:scale-95"
                >
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
