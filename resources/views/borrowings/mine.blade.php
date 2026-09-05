@extends('layouts.app')

@section('title', 'Peminjaman Saya | TE-Vault')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8" x-data="mineBorrowingsHandler()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Peminjaman Saya</h1>
        <p class="text-gray-500 mt-1">Riwayat & status peminjaman barang inventaris kamu</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($borrowings->count())

        <div class="space-y-4">
            @foreach ($borrowings as $borrowing)
                @php
                    $status = $borrowing->status->value ?? (string) $borrowing->status;
                    $isBorrowed = $status === 'borrowed';
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:shadow-md">

                    <div class="flex items-start gap-4">
                        {{-- Thumbnail --}}
                        <div class="w-16 h-16 rounded-xl bg-stone-100 dark:bg-stone-800 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center aspect-square">
                            @if ($borrowing->asset?->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($borrowing->asset->photo_path))
                                <img
                                    src="{{ asset('storage/' . $borrowing->asset->photo_path) }}"
                                    class="w-full h-full object-cover aspect-square"
                                    alt="{{ $borrowing->asset->name }}"
                                    width="64"
                                    height="64"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <svg class="w-7 h-7 text-stone-400 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>

                        <div>
                            @if ($borrowing->asset?->category)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    {{ $borrowing->asset->category->name }}
                                </span>
                            @endif

                            <p class="font-bold text-gray-800 text-base mt-1">{{ $borrowing->asset->name ?? '-' }}</p>
                            <p class="text-xs font-mono text-gray-500">{{ $borrowing->asset->asset_code ?? '-' }}</p>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                @if ($borrowing->borrowed_at)
                                    <span>Dipinjam: {{ $borrowing->borrowed_at->format('d M Y, H:i') }}</span>
                                @endif
                                @if ($borrowing->due_at)
                                    <span class="font-semibold {{ $borrowing->isOverdue() ? 'text-rose-600' : 'text-gray-700' }}">
                                        Batas: {{ $borrowing->due_at->format('d M Y, H:i') }}
                                        @if ($borrowing->isOverdue())
                                            (Terlambat)
                                        @endif
                                    </span>
                                @endif
                                @if ($borrowing->returned_at)
                                    <span class="text-emerald-700 font-medium">Dikembalikan: {{ $borrowing->returned_at->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 self-end md:self-center">
                        @php
                            $statusLabel = match ($status) {
                                'pending' => ['Menunggu Persetujuan', 'bg-yellow-100 text-yellow-800 border-yellow-200'],
                                'approved' => ['Disetujui', 'bg-blue-100 text-blue-800 border-blue-200'],
                                'borrowed' => ['Dipinjam', 'bg-rose-100 text-rose-800 border-rose-200'],
                                'return_pending_verification' => ['Menunggu Verifikasi', 'bg-purple-100 text-purple-800 border-purple-200'],
                                'returned' => ['Selesai', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                                'rejected' => ['Ditolak', 'bg-gray-100 text-gray-600 border-gray-200'],
                                'cancelled' => ['Dibatalkan', 'bg-gray-100 text-gray-600 border-gray-200'],
                                default => [ucfirst($status), 'bg-gray-100 text-gray-600 border-gray-200'],
                            };
                        @endphp

                        <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusLabel[1] }}">
                            {{ $statusLabel[0] }}
                        </span>

                        @if ($isBorrowed)
                            <button
                                type="button"
                                @click="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->asset->name ?? 'Aset') }}', '{{ $borrowing->asset->asset_code ?? '' }}')"
                                class="px-4 py-2 text-sm font-medium w-full sm:w-auto rounded-lg transition-colors bg-[#6F4E37] text-white hover:bg-[#5a3f2c] flex items-center justify-center gap-1.5 shadow-sm active:scale-95"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                Kembalikan Barang
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $borrowings->links() }}
        </div>

    @else

        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-200">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Belum Ada Riwayat Peminjaman</h3>
            <p class="text-gray-500 text-xs mt-1">Kamu belum pernah meminjam barang inventaris.</p>
            <div class="mt-5">
                <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#6F4E37] text-white text-xs font-semibold hover:bg-[#5a3f2c] transition">
                    Lihat Katalog Barang
                </a>
            </div>
        </div>

    @endif

    {{-- Modal Pengembalian Barang Real-Time Webcam --}}
    <div
        x-show="modalOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div class="max-w-lg w-full mx-auto sm:rounded-xl bg-white shadow-2xl border border-gray-200 overflow-hidden" @click.away="closeReturnModal()">
            
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Pengembalian Barang</h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="activeAssetName + ' (' + activeAssetCode + ')'"></p>
                </div>
                <button type="button" @click="closeReturnModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">
                    ✕
                </button>
            </div>

            {{-- Modal Body Form --}}
            <form :action="'/borrowings/' + activeBorrowingId + '/return-request'" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" @submit="onReturnSubmit($event)">
                @csrf

                <div class="rounded-xl bg-blue-50 border border-blue-200 p-3.5 text-xs text-blue-800 leading-relaxed">
                    Ambil foto barang yang dikembalikan secara real-time. Status peminjaman akan langsung diselesaikan dan aset kembali tersedia di katalog.
                </div>

                {{-- Modul Kamera Real-time Webcam --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-800">
                            Foto Bukti Pengembalian Real-Time <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-medium text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Wajib Kamera Real-Time
                        </span>
                    </div>

                    <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-3">
                        <div class="relative aspect-video w-full overflow-hidden rounded-lg bg-gray-950 flex items-center justify-center shadow-inner">
                            
                            {{-- Live Video Stream --}}
                            <video x-ref="modalVideo" autoplay playsinline class="h-full w-full object-cover" :class="{ 'hidden': (capturedImage || returnCapturedPhoto) || !(isCameraOpen || isModalCameraOpen) }"></video>

                            {{-- Captured Photo Preview --}}
                            <img x-show="capturedImage || returnCapturedPhoto" :src="capturedImage || returnCapturedPhoto" class="h-full w-full object-cover" alt="Foto Pengembalian">

                            {{-- Hidden Canvas --}}
                            <canvas x-ref="modalCanvas" class="hidden"></canvas>

                            {{-- Placeholder jika kamera belum aktif --}}
                            <div x-show="!(isCameraOpen || isModalCameraOpen) && !(capturedImage || returnCapturedPhoto)" class="flex flex-col items-center justify-center p-4 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-2 text-gray-300 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-white font-medium">Kamera Belum Aktif</span>
                                <p class="text-[11px] text-gray-400 mt-1 max-w-xs">Tekan tombol "Buka Kamera" di bawah untuk mulai mengambil foto pengembalian.</p>
                            </div>

                            {{-- Live Badge Overlay --}}
                            <div x-show="(isCameraOpen || isModalCameraOpen) && !(capturedImage || returnCapturedPhoto)" class="absolute top-2.5 left-2.5 flex items-center gap-1.5 bg-black/60 backdrop-blur-md text-white text-[11px] px-2.5 py-0.5 rounded-full pointer-events-none z-10">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                <span class="font-medium">Live</span>
                            </div>

                            {{-- Captured Success Badge --}}
                            <div x-show="capturedImage || returnCapturedPhoto" class="absolute top-2.5 left-2.5 flex items-center gap-1 bg-emerald-600/90 text-white text-[11px] px-2.5 py-0.5 rounded-full shadow backdrop-blur-sm pointer-events-none z-10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Foto Siap Disimpan</span>
                            </div>

                        </div>

                        {{-- Bar Kontrol Khusus di Bawah Kotak Kamera --}}
                        <div>
                            {{-- State 1: Kamera Belum Aktif --}}
                            <div x-show="!(isCameraOpen || isModalCameraOpen) && !(capturedImage || returnCapturedPhoto)" class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-2.5 p-1.5">
                                <button
                                    type="button"
                                    @click="openModalCamera()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl bg-[#6F4E37] text-white text-xs font-semibold hover:bg-[#5a3f2c] transition shadow-md active:scale-95 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Buka Kamera</span>
                                </button>
                                <p class="text-[11px] text-gray-500 text-center sm:text-right">
                                    Izinkan akses kamera pada browser untuk verifikasi barang.
                                </p>
                            </div>

                            {{-- State 2: Kamera Aktif (Live Camera Control Bar) --}}
                            <div x-show="(isCameraOpen || isModalCameraOpen) && !(capturedImage || returnCapturedPhoto)" class="mt-3 flex items-center justify-center gap-4">
                                {{-- Tombol Shutter Kamera Utama Selalu Ter-render di Tengah --}}
                                <button 
                                    type="button" 
                                    @click="takeSnapshot()" 
                                    class="w-14 h-14 rounded-full border-4 border-[#6F4E37] bg-white shadow-lg active:scale-95 transition-transform flex items-center justify-center hover:bg-stone-50 cursor-pointer"
                                    title="Ambil Foto Bukti">
                                    <div class="w-10 h-10 rounded-full bg-[#6F4E37] flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                </button>

                                {{-- Tombol Flip Kamera di Sebelah Kanan Tombol Shutter --}}
                                <button
                                    type="button"
                                    @click="switchCamera()"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-xs font-medium transition shadow-xs active:scale-95 cursor-pointer"
                                    title="Ganti Kamera Depan/Belakang"
                                >
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span class="hidden sm:inline">Ganti Kamera</span>
                                </button>
                            </div>

                            {{-- State 3: Foto Siap Disimpan (Tombol Bersih di Bawah Kanvas) --}}
                            <div x-show="capturedImage || returnCapturedPhoto" class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-2.5 p-1.5">
                                <div class="text-xs text-emerald-700 font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Foto bukti fisik barang berhasil diambil.</span>
                                </div>

                                <button
                                    type="button"
                                    @click="retakePhoto()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold shadow-xs transition active:scale-95 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>Ambil Ulang Foto</span>
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="return_evidence" :value="capturedImage || returnCapturedPhoto">
                    </div>
                </div>

                {{-- Catatan Pengembalian --}}
                <div>
                    <label for="return_note" class="block text-xs font-semibold text-gray-800">
                        Catatan Kondisi Barang <span class="font-normal text-gray-400">(opsional)</span>
                    </label>
                    <textarea
                        id="return_note"
                        name="return_note"
                        rows="2"
                        placeholder="Contoh: Dikembalikan dalam keadaan baik dan lengkap..."
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs shadow-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600"
                    ></textarea>
                </div>

                {{-- Modal Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="closeReturnModal()" class="px-4 py-2 rounded-xl border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-sm">
                        Konfirmasi Pengembalian
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
    function mineBorrowingsHandler() {
        return {
            modalOpen: false,
            activeBorrowingId: null,
            activeAssetName: '',
            activeAssetCode: '',
            isCameraOpen: false,
            isModalCameraOpen: false,
            capturedImage: null,
            returnCapturedPhoto: null,
            modalMediaStream: null,
            modalFacingMode: 'environment',

            openReturnModal(borrowingId, name, code) {
                this.activeBorrowingId = borrowingId;
                this.activeAssetName = name;
                this.activeAssetCode = code;
                this.capturedImage = null;
                this.returnCapturedPhoto = null;
                this.modalOpen = true;
                this.openModalCamera();
            },

            closeReturnModal() {
                this.closeModalCamera();
                this.modalOpen = false;
            },

            async openModalCamera() {
                try {
                    if (this.modalMediaStream) {
                        this.closeModalCamera();
                    }
                    this.modalMediaStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: this.modalFacingMode,
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    });
                    this.$refs.modalVideo.srcObject = this.modalMediaStream;
                    this.isCameraOpen = true;
                    this.isModalCameraOpen = true;
                    this.capturedImage = null;
                    this.returnCapturedPhoto = null;
                } catch (err) {
                    console.error("Modal camera access error:", err);
                }
            },

            applyModalWatermark(canvas, userName) {
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
                const textStr = `Pengembalian: ${userName} | ${timestampStr}`;

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

                // White text
                ctx.fillStyle = '#FFFFFF';
                ctx.textBaseline = 'middle';
                ctx.fillText(textStr, x + paddingX, y + (boxHeight / 2));
            },

            takeSnapshot() {
                this.snapModalSnapshot();
            },

            snapModalSnapshot() {
                const video = this.$refs.modalVideo;
                const canvas = this.$refs.modalCanvas;
                if (!video || !canvas) return;

                const maxDim = 1280;
                let w = video.videoWidth || 640;
                let h = video.videoHeight || 480;

                // Batasi dimensi maksimal canvas 1280px dengan menjaga aspect ratio
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
                this.applyModalWatermark(canvas, "{{ auth()->user()->name }}");

                // Konversi Base64 dengan quality 0.68 (rentang 0.65 - 0.70) agar ukuran file awal di 100-200 KB
                const base64 = canvas.toDataURL('image/jpeg', 0.68);
                this.capturedImage = base64;
                this.returnCapturedPhoto = base64;
                this.closeModalCamera();
            },

            retakePhoto() {
                this.retakeModalSnapshot();
            },

            retakeModalSnapshot() {
                this.capturedImage = null;
                this.returnCapturedPhoto = null;
                this.openModalCamera();
            },

            switchCamera() {
                this.switchModalFacingMode();
            },

            switchModalFacingMode() {
                this.modalFacingMode = this.modalFacingMode === 'user' ? 'environment' : 'user';
                this.openModalCamera();
            },

            closeModalCamera() {
                if (this.modalMediaStream) {
                    this.modalMediaStream.getTracks().forEach(t => t.stop());
                    this.modalMediaStream = null;
                }
                this.isCameraOpen = false;
                this.isModalCameraOpen = false;
            },

            onReturnSubmit(e) {
                if (!this.capturedImage && !this.returnCapturedPhoto) {
                    e.preventDefault();
                    alert('Silakan ambil foto bukti fisik pengembalian barang via kamera real-time terlebih dahulu.');
                    return;
                }
                this.closeModalCamera();
            }
        }
    }
</script>

@endsection
