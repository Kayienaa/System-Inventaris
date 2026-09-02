@extends('layouts.app')

@section('title', 'Form Peminjaman Barang | TE-Vault')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-8">

        <div class="mb-8">
            <a
                href="{{ route('assets.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Katalog
            </a>

            <h1 class="mt-4 text-3xl font-bold text-gray-800">
                Peminjaman Aset (Instant Borrow)
            </h1>

            <p class="mt-1 text-gray-500">
                Ambil foto serah terima secara real-time via kamera untuk langsung memproses peminjaman.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- Informasi Aset yang Dipilih --}}
            <div class="border-b border-gray-100 bg-gradient-to-r from-amber-50/60 to-orange-50/40 p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    <div class="h-36 w-full overflow-hidden rounded-xl bg-gray-100 sm:w-48 shrink-0 border border-gray-200 shadow-sm relative">
                        @php
                            $photoExists = $asset->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->photo_path);
                        @endphp

                        @if ($photoExists)
                            <img
                                src="{{ asset('storage/' . $asset->photo_path) }}"
                                alt="{{ $asset->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full flex-col items-center justify-center text-xs text-gray-400 bg-gray-50">
                                <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Foto tidak tersedia
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if ($asset->category)
                            <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-md border border-amber-200 mb-1.5">
                                {{ $asset->category->name }}
                            </span>
                        @endif

                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $asset->name }}
                        </h2>

                        <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                            <div><span class="text-gray-400">Kode Aset:</span> <span class="font-mono font-semibold text-gray-800">{{ $asset->asset_code }}</span></div>
                            @if ($asset->brand)
                                <div><span class="text-gray-400">Merk:</span> <span class="font-medium text-gray-800">{{ $asset->brand }}</span></div>
                            @endif
                            @if ($asset->model)
                                <div><span class="text-gray-400">Model:</span> <span class="font-medium text-gray-800">{{ $asset->model }}</span></div>
                            @endif
                            @if ($asset->serial_number)
                                <div><span class="text-gray-400">Serial No:</span> <span class="font-mono text-gray-800">{{ $asset->serial_number }}</span></div>
                            @endif
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                ● Status: Siap Dipinjam
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Form Peminjaman Langsung --}}
            <form
                action="{{ route('assets.borrow.store', $asset) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6 p-6 sm:p-8"
                x-data="borrowWebcamHandler()"
                @submit="onFormSubmit($event)"
            >
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">

                {{-- Alert Info Instant Borrow --}}
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs text-emerald-800 leading-relaxed">
                        <span class="font-bold">Instant Borrowing Active:</span> Setelah disubmit dengan foto bukti, peminjaman langsung aktif dengan status <strong>Dipinjam</strong> dan tenggat otomatis <strong>H+3</strong>.
                    </div>
                </div>

                {{-- Modul Kamera Real-time Webcam --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-800">
                            Foto Bukti Serah Terima Real-Time <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs text-gray-500">Kamera Device / Webcam</span>
                    </div>

                    <div class="rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 transition hover:border-gray-400">

                        {{-- Viewport Kamera --}}
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
                                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-2.5 text-gray-300">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-white">Kamera Belum Aktif</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs">Klik tombol "Buka Kamera" untuk mengaktifkan webcam atau kamera HP.</p>
                            </div>

                            {{-- Live Badge Overlay --}}
                            <div x-show="isCameraOpen && !capturedPhoto" class="absolute top-3 left-3 flex items-center gap-2 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                <span class="font-medium">Live Camera</span>
                            </div>

                            {{-- Flip Camera Button --}}
                            <button
                                type="button"
                                x-show="isCameraOpen && !capturedPhoto"
                                @click="switchFacingMode()"
                                class="absolute top-3 right-3 p-2 rounded-full bg-black/60 text-white hover:bg-black/80 transition backdrop-blur-md"
                                title="Ganti Kamera Depan/Belakang"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>

                            {{-- Captured Success Badge --}}
                            <div x-show="capturedPhoto" class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-emerald-600/90 text-white text-xs px-3 py-1 rounded-full shadow-md backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Foto Siap Disimpan</span>
                            </div>

                        </div>

                        {{-- Action Buttons Kamera --}}
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    x-show="!isCameraOpen && !capturedPhoto"
                                    @click="openCamera()"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#6F4E37] text-white text-xs font-semibold hover:bg-[#5a3f2c] transition shadow-sm active:scale-95"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Buka Kamera
                                </button>

                                <button
                                    type="button"
                                    x-show="isCameraOpen && !capturedPhoto"
                                    @click="snapSnapshot()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition shadow-sm active:scale-95 ring-2 ring-emerald-400/50"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    </svg>
                                    Ambil Foto
                                </button>

                                <button
                                    type="button"
                                    x-show="capturedPhoto"
                                    @click="retakeSnapshot()"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-700 text-white text-xs font-medium hover:bg-gray-800 transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Ambil Ulang
                                </button>
                            </div>

                            {{-- Alternatif File Upload --}}
                            <div class="text-xs text-gray-500">
                                Atau pilih file: 
                                <input
                                    type="file"
                                    name="borrowing_evidence_file"
                                    accept="image/*"
                                    @change="onFileChosen($event)"
                                    class="ml-1 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300"
                                >
                            </div>
                        </div>

                        {{-- Hidden Input for Base64 Data --}}
                        <input type="hidden" name="borrowing_evidence" :value="capturedPhoto">

                    </div>

                    @error('borrowing_evidence')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tanggal Rencana Pengembalian --}}
                <div>
                    <label
                        for="due_at"
                        class="block text-sm font-semibold text-gray-800"
                    >
                        Tenggat Waktu Pengembalian (Default H+3)
                    </label>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Secara otomatis diatur 3 hari dari sekarang. Anda dapat menyesuaikannya jika diperlukan.
                    </p>

                    <input
                        id="due_at"
                        name="due_at"
                        type="datetime-local"
                        value="{{ old('due_at', now()->addDays(3)->format('Y-m-d\TH:i')) }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600"
                    >

                    @error('due_at')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Catatan Peminjam --}}
                <div>
                    <label
                        for="borrower_note"
                        class="block text-sm font-semibold text-gray-800"
                    >
                        Catatan Keperluan
                        <span class="font-normal text-gray-400">(opsional)</span>
                    </label>

                    <textarea
                        id="borrower_note"
                        name="borrower_note"
                        rows="2"
                        maxlength="1000"
                        placeholder="Contoh: Praktikum Mobile App TEFA di Ruang 2..."
                        class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600"
                    >{{ old('borrower_note') }}</textarea>

                    @error('borrower_note')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="flex flex-col-reverse gap-3 pt-4 border-t border-gray-100 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('assets.index') }}"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-[#6F4E37] px-7 py-2.5 text-sm font-bold text-white hover:bg-[#5a3f2c] transition shadow-md active:scale-[0.98] flex items-center justify-center gap-2"
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
                        alert("Tidak dapat mengakses kamera perangkat. Pastikan izin kamera aktif atau gunakan opsi pilih file di sebelah kanan.");
                    }
                },

                snapSnapshot() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    if (!video || !canvas) return;

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
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

                onFileChosen(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.capturedPhoto = e.target.result;
                        this.closeCamera();
                    };
                    reader.readAsDataURL(file);
                },

                onFormSubmit(e) {
                    // Stop camera stream before navigating
                    this.closeCamera();
                }
            }
        }
    </script>
@endsection
