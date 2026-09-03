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
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ route('admin.assets.index') }}" class="hover:text-amber-700 transition">Master Aset</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Edit Aset ({{ $asset->asset_code }})</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                    Edit Informasi Aset
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Perbarui data spesifikasi, nomor seri, kondisi, status ketersediaan, atau foto unit.
                </p>
            </div>
            <a
                href="{{ route('admin.assets.index') }}"
                class="px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
            >
                Kembali
            </a>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Terdapat kesalahan pengisian form:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
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
                            <label for="asset_category_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Kategori Aset <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="asset_category_id"
                                name="asset_category_id"
                                required
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('asset_category_id') border-rose-400 @enderror"
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
                            <label for="asset_code" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Kode Aset Unik <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="asset_code"
                                type="text"
                                name="asset_code"
                                value="{{ old('asset_code', $asset->asset_code) }}"
                                required
                                maxlength="50"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm font-mono transition @error('asset_code') border-rose-400 @enderror"
                            >
                            @error('asset_code')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 2: Nama Aset & Nomor Seri --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Nama Barang / Unit <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $asset->name) }}"
                                required
                                maxlength="255"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('name') border-rose-400 @enderror"
                            >
                            @error('name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="serial_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Nomor Seri (Serial Number) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="serial_number"
                                type="text"
                                name="serial_number"
                                value="{{ old('serial_number', $asset->serial_number) }}"
                                required
                                maxlength="100"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm font-mono transition @error('serial_number') border-rose-400 @enderror"
                            >
                            @error('serial_number')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 3: Merk & Model --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="brand" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Merk / Brand <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="brand"
                                type="text"
                                name="brand"
                                value="{{ old('brand', $asset->brand) }}"
                                required
                                maxlength="100"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('brand') border-rose-400 @enderror"
                            >
                            @error('brand')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Model / Tipe Spesifik
                            </label>
                            <input
                                id="model"
                                type="text"
                                name="model"
                                value="{{ old('model', $asset->model) }}"
                                maxlength="100"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('model') border-rose-400 @enderror"
                            >
                            @error('model')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 4: Kondisi & Status Ketersediaan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="condition" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Kondisi Fisik <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="condition"
                                name="condition"
                                required
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('condition') border-rose-400 @enderror"
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
                            <label for="availability_status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Status Ketersediaan <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="availability_status"
                                name="availability_status"
                                required
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition @error('availability_status') border-rose-400 @enderror"
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
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Catatan / Spesifikasi Tambahan
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm transition"
                        >{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Kolom Kanan: Tinjauan Foto Fisik Aset & Upload (1/3) --}}
                <div class="lg:col-span-4 space-y-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Foto Fisik Aset
                    </label>

                    @php
                        $existingPhoto = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                    @endphp

                    {{-- Kontainer Pembatas Proporsional Preview Foto --}}
                    <div class="max-w-xs md:max-w-sm rounded-xl overflow-hidden border border-gray-200 bg-gray-50 p-2 shadow-sm">
                        <template x-if="photoPreview">
                            <div>
                                <img :src="photoPreview" alt="Pratinjau Foto Baru" class="w-full h-48 md:h-56 object-cover rounded-lg">
                                <p class="text-[11px] font-semibold text-amber-700 text-center mt-2">Pratinjau Foto Baru</p>
                            </div>
                        </template>

                        <template x-if="!photoPreview">
                            <div>
                                @if ($existingPhoto)
                                    <img src="{{ asset('storage/' . $asset->photo_path) }}" alt="{{ $asset->name }}" class="w-full h-48 md:h-56 object-cover rounded-lg">
                                    <p class="text-[11px] text-gray-400 text-center mt-2">Foto Saat Ini</p>
                                @else
                                    <div class="w-full h-48 md:h-56 rounded-lg bg-gray-100 flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs">Belum ada foto unit</span>
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
                            class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer"
                        >
                        <p class="text-[11px] text-gray-400 mt-2">
                            Pilih file gambar baru jika ingin mengganti foto saat ini (Maks 2MB, JPG/PNG/WEBP). Foto lama akan otomatis digantikan.
                        </p>
                        @error('photo')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- Form Actions --}}
            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.assets.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white text-xs font-bold shadow-md shadow-[#6F4E37]/20 transition-all duration-150 active:scale-95"
                >
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
