@extends('layouts.app')

@section('title', 'Panduan SOP Operasional Admin & Super Admin - SITEFA SMKN 1 Bangsri')

@section('content')
@php
$steps = [
    [
        'number' => 1,
        'slug' => 'monitoring',
        'sop' => 'SOP-ADM-01 · DASHBOARD METRICS',
        'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
        'number_gradient' => 'from-amber-600 to-amber-800 text-white',
        'title' => 'Monitoring Dasbor & Metrik Transaksi Lab',
        'summary' => 'Pemantauan statistik real-time ketersediaan aset, transaksi berjalan, dan metrik mingguan operasional.',
        'details' => [
            'Buka menu Dashboard Admin untuk meninjau ringkasan metrik inventaris dan grafik transaksi secara komprehensif.',
            'Periksa kartu metrik utama: total unit aset terdaftar (32 unit), unit sedang dipinjam, unit menunggu persetujuan, dan unit terlambat (overdue).',
            'Gunakan ringkasan aktivitas mingguan untuk memantau frekuensi sirkulasi dan lonjakan peminjaman barang di laboratorium TEFA.',
        ],
        'tip' => 'Tinjau angka unit terlambat secara rutin setiap pergantian jam dan sore hari untuk segera menindaklanjuti pengembalian yang melewati batas waktu.',
        'desktop_img' => 'admin-step-1-monitoring.png',
        'mobile_img' => 'admin-step-1-monitoring-mobile.png',
        'action_label' => 'Buka Dashboard Admin',
        'action_url' => route('dashboard'),
    ],
    [
        'number' => 2,
        'slug' => 'approval',
        'sop' => 'SOP-ADM-02 · PERSETUJUAN TRANSAKSI',
        'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
        'number_gradient' => 'from-emerald-600 to-emerald-800 text-white',
        'title' => 'Review & Persetujuan Permohonan (Approval / Reject)',
        'summary' => 'Pemeriksaan kelayakan pemohon, verifikasi keperluan peminjaman, serta penetapan keputusan permohonan.',
        'details' => [
            'Buka menu "Monitoring Peminjaman" untuk melihat daftar permohonan peminjaman yang masuk dengan status "Menunggu Persetujuan".',
            'Periksa informasi pemohon: identitas siswa/guru, kelas, kontak, keperluan peminjaman, dan estimasi waktu pengembalian.',
            'Klik tombol hijau "Setujui" untuk meloloskan permohonan, atau klik tombol merah "Tolak" dengan menyertakan alasan penolakan yang jelas.',
        ],
        'tip' => 'sebelum disetujui, holding timer 10 menit otomatis aktif. Peminjam harus segera hadir di ruang lab untuk verifikasi ulang secara langsung dan serah terima fisik.',
        'desktop_img' => 'admin-step-2-approval.png',
        'mobile_img' => 'admin-step-2-approval-mobile.png',
        'action_label' => 'Monitoring Peminjaman',
        'action_url' => route('admin.borrowings.index'),
    ],
    [
        'number' => 3,
        'slug' => 'whatsapp-shortcut',
        'sop' => 'SOP-ADM-03 · KOMUNIKASI & NOTIFIKASI',
        'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
        'number_gradient' => 'from-emerald-600 to-teal-800 text-white',
        'title' => 'Pengawasan Transaksi Aktif & Pintasan WhatsApp',
        'summary' => 'Pelacakan status transaksi aktif dan komunikasi instan via WhatsApp untuk pengingat pengambilan atau pengembalian.',
        'details' => [
            'Pantau daftar transaksi pada tabel monitoring peminjaman yang berada pada tahap "Disetujui" maupun "Sedang Dipinjam".',
            'Gunakan tombol pintasan WhatsApp di samping nama peminjam untuk membuka percakapan langsung dengan nomor terdaftar.',
            'Sistem otomatis memformat pesan WhatsApp berisi detail nomor tiket transaksi, nama unit barang, dan tenggat waktu pengembalian.',
        ],
        'tip' => 'Anda akan langsung diarahkan ke nomor peminjam dan sudah terisi otomatis template untuk peringatan pengembalian ketika mengklik "kirim whatsapp".',
        'desktop_img' => 'admin-step-3-whatsapp-shortcut.png',
        'mobile_img' => 'admin-step-3-whatsapp-shortcut-mobile.png',
        'action_label' => 'Pantau Transaksi Aktif',
        'action_url' => route('admin.borrowings.index'),
    ],
    [
        'number' => 4,
        'slug' => 'bukti-serah-terima',
        'sop' => 'SOP-ADM-04 · SERAH TERIMA FISIK',
        'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800',
        'number_gradient' => 'from-indigo-600 to-indigo-800 text-white',
        'title' => 'Pendampingan Kamera Serah Terima Real-Time di Lab',
        'summary' => 'Pendampingan penyerahan unit fisik di lab dan pengambilan foto bukti fisik melalui kamera web sistem.',
        'details' => [
            'Peminjam hadir secara fisik di Lab RPL dan mengonfirmasi nomor permohonan kepada Mas Donny.',
            'Lakukan pengecekan fisik unit bersama peminjam (kondisi fisik, baterai, kabel charger/adaptor, dan aksesoris bawaan).',
            'Lakukan foto secara real-time bersama si peminjam dan barang yang akan dipinjam".',
        ],
        'tip' => 'Pastikan nomor seri dan kelengkapan aksesoris tampak jelas pada foto serah terima sebagai dokumen bukti serah terima autentik.',
        'desktop_img' => 'admin-step-4-bukti-serah-terima.png',
        'mobile_img' => 'admin-step-4-bukti-serah-terima-mobile.png',
        'action_label' => 'Daftar Serah Terima',
        'action_url' => route('admin.borrowings.index'),
    ],
    [
        'number' => 5,
        'slug' => 'verifikasi-kembali',
        'sop' => 'SOP-ADM-05 · VERIFIKASI PENGEMBALIAN',
        'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
        'number_gradient' => 'from-amber-600 to-amber-800 text-white',
        'title' => 'Inspeksi Fisik & Verifikasi Pengembalian Unit',
        'summary' => 'Inspeksi kondisi barang saat pengembalian, pemotretan dokumentasi akhir, serta penetapan status kondisi barang.',
        'details' => [
            'Terima unit yang dikembalikan oleh peminjam di ruang lab dan periksa fungsi serta kelengkapan fisiknya.',
            'Buka fitur verifikasi pengembalian pada sistem, lalu ambil foto kondisi akhir barang melalui kamera web.',
            'Tentukan kondisi barang: pilih "Baik" (otomatis kembali tersedia di katalog), "Rusak Ringan", atau "Rusak Berat" (karantina maintenance).',
            'Simpan verifikasi pengembalian untuk menutup siklus transaksi secara resmi di sistem SITEFA.',
        ],
        'tip' => 'Apabila unit mengalami kerusakan, catat kronologi lengkap dan pilih status kondisi yang sesuai agar unit masuk karantina perawatan.',
        'desktop_img' => 'admin-step-5-verifikasi-kembali.png',
        'mobile_img' => 'admin-step-5-verifikasi-kembali-mobile.png',
        'action_label' => 'Verifikasi Pengembalian',
        'action_url' => route('admin.borrowings.index'),
    ],
    [
        'number' => 6,
        'slug' => 'master-aset',
        'sop' => 'SOP-ADM-06 · MASTER DATA ASET',
        'badge_color' => 'bg-cyan-100 text-cyan-800 border-cyan-300 dark:bg-cyan-950/50 dark:text-cyan-300 dark:border-cyan-800',
        'number_gradient' => 'from-cyan-600 to-cyan-800 text-white',
        'title' => 'Pengelolaan Master Inventaris 32 Unit Aset',
        'summary' => 'Pembaruan data inventaris barang, penambahan unit baru, pemutakhiran serial number, dan pengaturan status pemeliharaan.',
        'details' => [
            'Buka menu "Kelola Aset" untuk mengakses tabel master seluruh 32 unit aset lab TEFA.',
            'Kelola informasi barang: tambah aset baru, perbarui nama alat, nomor seri, spesifikasi teknis, serta kategori alat.',
            'Atur status operasional unit: "Tersedia", "Dipinjam", atau "Maintenance/Perbaikan" sesuai kondisi fisik di lab.',
        ],
        'tip' => 'Selalu periksa kesesuaian label barcode/nomor seri fisik pada barang dengan kode inventaris yang tercatat di sistem.',
        'desktop_img' => 'admin-step-6-master-aset.png',
        'mobile_img' => 'admin-step-6-master-aset-mobile.png',
        'action_label' => 'Kelola Master Aset',
        'action_url' => route('admin.assets.index'),
    ],
    [
        'number' => 7,
        'slug' => 'sipintu-gateway',
        'sop' => 'SOP-ADM-07 · INTEGRASI SIPINTU',
        'badge_color' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
        'number_gradient' => 'from-purple-600 to-purple-800 text-white',
        'title' => 'Sinkronisasi Terpadu Gateway SiPintu (Super Admin)',
        'summary' => 'Sinkronisasi data master pengguna sekolah (siswa, guru, rombel) secara terpadu melalui API gateway SiPintu.',
        'details' => [
            'Khusus Super Admin: Buka menu "Gateway SiPintu" pada navigasi sidebar.',
            'Tinjau status koneksi API, waktu sinkronisasi terakhir, dan total rekaman data siswa serta guru.',
            'Klik tombol "Sinkronisasi Sekarang" untuk memperbarui data pengguna lokal dari server pusat SiPintu SMKN 1 Bangsri.',
        ],
        'tip' => 'Jadwalkan sinkronisasi data pada awal tahun ajaran baru atau saat terjadi mutasi kelas agar akun siswa selalu mutakhir.',
        'desktop_img' => 'admin-step-7-sipintu-gateway.png',
        'mobile_img' => 'admin-step-7-sipintu-gateway-mobile.png',
        'action_label' => 'Gateway SiPintu',
        'action_url' => route('sipintu.index'),
    ],
    [
        'number' => 8,
        'slug' => 'audit-log',
        'sop' => 'SOP-ADM-08 · AUDIT & REKAP LAPORAN',
        'badge_color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
        'number_gradient' => 'from-rose-600 to-rose-800 text-white',
        'title' => 'Forensik Audit Log & Ekspor Rekapitulasi Laporan',
        'summary' => 'Pemantauan jejak audit aktivitas sistem dan ekspor laporan transaksi peminjaman dalam format Excel/CSV & PDF.',
        'details' => [
            'Khusus Super Admin: Buka menu "Audit Log" untuk melacak riwayat aksi (login, persetujuan, checkout, pengembalian, edit aset).',
            'Gunakan filter pencarian berdasarkan rentang waktu, nama user, atau tipe aksi untuk investigasi forensik.',
            'Akses menu "Laporan" atau tombol ekspor pada tabel monitoring untuk mengunduh rekapitulasi data peminjaman dalam format Excel atau PDF.',
        ],
        'tip' => 'Ekspor laporan berkala dapat diarsipkan sebagai dokumen fisik bukti pertanggungjawaban pengelolaan sarana prasarana TEFA.',
        'desktop_img' => 'admin-step-8-audit-log.png',
        'mobile_img' => 'admin-step-8-audit-log-mobile.png',
        'action_label' => 'Buka Audit Log',
        'action_url' => route('admin.audit-logs.index'),
    ],
];
@endphp

<div x-data="{
        device: 'desktop',
        zoomModal: false,
        activeZoomImg: '',
        activeZoomTitle: '',
        activeStep: 1
     }"
     class="space-y-8 max-w-7xl mx-auto">

    {{-- ========================================================
         HERO BANNER: CLEAN VINTAGE BROWN & DARK MODE
    ======================================================== --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4A3022] via-[#6F4E37] to-[#3D2817] text-white p-6 sm:p-10 shadow-xl border border-[#8B6A4F]/30 dark:from-[#0E1420] dark:via-[#131B2A] dark:to-[#0B0F17] dark:border-stone-800/80">
        {{-- Background decorative shapes --}}
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-amber-500/15 dark:bg-cyan-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-56 h-56 rounded-full bg-[#C89B3C]/20 dark:bg-cyan-500/5 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 dark:bg-cyan-500/10 text-amber-200 dark:text-cyan-300 border border-white/15 dark:border-cyan-500/20 backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 text-amber-300 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-17.25c0-.621-.504-1.125-1.125-1.125h-4.5M10.125 2.25A2.25 2.25 0 007.875 4.5h8.25a2.25 2.25 0 00-2.25-2.25m-3.75 0h3.75m-4.5 9.75l2.25 2.25 4.5-4.5" />
                    </svg>
                    <span>SOP OPERASIONAL ADMIN &amp; SUPER ADMIN SITEFA</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-heading text-white tracking-tight leading-tight">
                    Panduan SOP Operasional Administrator &amp; Teknisi Lab
                </h1>

                <p class="text-sm sm:text-base text-stone-200/90 dark:text-stone-300 leading-relaxed">
                    Pelajari <strong class="text-amber-300 dark:text-cyan-300 font-semibold">8 langkah SOP operasional terstruktur</strong> untuk Administrator dan Super Administrator. Panduan mencakup monitoring metrik, persetujuan transaksi, pendampingan kamera serah terima, inspeksi pengembalian, master 31 unit aset, integrasi SiPintu, hingga forensik audit log.
                </p>

                {{-- Fast Metric Badges --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">8 Langkah</div>
                        <div class="text-stone-300 text-[11px]">SOP Operasional</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">10 Menit</div>
                        <div class="text-stone-300 text-[11px]">Holding Reservasi</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">Kamera Web</div>
                        <div class="text-stone-300 text-[11px]">Serah Terima &amp; Kembali</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">SiPintu &amp; Audit</div>
                        <div class="text-stone-300 text-[11px]">Integrasi Super Admin</div>
                    </div>
                </div>
            </div>

            {{-- Quick action card --}}
            <div class="shrink-0 flex flex-col sm:flex-row lg:flex-col gap-3">
                <a href="{{ route('admin.borrowings.index') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl font-semibold text-sm bg-gradient-to-r from-amber-400 to-[#C89B3C] text-[#30251F] hover:from-amber-300 hover:to-amber-400 dark:from-cyan-400 dark:to-cyan-500 dark:text-stone-950 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-95 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                    <span>Monitoring Peminjaman</span>
                </a>

                <a href="{{ route('admin.assets.index') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl font-semibold text-sm bg-white/10 hover:bg-white/20 text-white dark:bg-stone-800/80 dark:hover:bg-stone-800 border border-white/20 dark:border-stone-700 transition-all duration-200 active:scale-95 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                    </svg>
                    <span>Kelola Master Aset</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================
         DEVICE MODE SWITCHER & QUICK STEP JUMP PILLS
    ======================================================== --}}
    <div class="sticky top-2 sm:top-4 z-30 bg-white/90 dark:bg-[#0E1420]/90 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-stone-200/80 dark:border-stone-800 shadow-md">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            {{-- Mode Switcher Buttons --}}
            <div class="w-full md:w-auto flex items-center justify-between sm:justify-start gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 hidden sm:inline-block">
                    Pilih Tampilan:
                </span>
                <div class="inline-flex p-1 rounded-xl bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 w-full sm:w-auto">
                    {{-- Tab 1: Desktop / PC --}}
                    <button type="button"
                            @click="device = 'desktop'"
                            :class="device === 'desktop'
                                ? 'bg-white dark:bg-[#131B2A] text-amber-900 dark:text-cyan-300 shadow-xs font-bold'
                                : 'text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-200 font-medium'"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                        </svg>
                        <span>Mode Komputer / Desktop</span>
                    </button>

                    {{-- Tab 2: Mobile / HP --}}
                    <button type="button"
                            @click="device = 'mobile'"
                            :class="device === 'mobile'
                                ? 'bg-white dark:bg-[#131B2A] text-amber-900 dark:text-cyan-300 shadow-xs font-bold'
                                : 'text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-200 font-medium'"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                        <span>Mode Ponsel / HP</span>
                    </button>
                </div>
            </div>

            {{-- Info indicator --}}
            <div class="text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5 self-end md:self-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Menampilkan tangkapan layar: <strong class="text-stone-800 dark:text-stone-200" x-text="device === 'desktop' ? 'Versi Desktop / Layar Lebar' : 'Versi Mobile / Ponsel'"></strong></span>
            </div>
        </div>

        {{-- Step Quick-Jump Pills with Custom Tailwind Slim Scrollbar --}}
        <div class="mt-3 pt-2.5 border-t border-stone-200/70 dark:border-stone-800/80">
            <div class="flex items-center gap-2 overflow-x-auto pb-2.5 pt-1 px-1 scroll-smooth [scrollbar-gutter:stable]
                   [&::-webkit-scrollbar]:h-1.5
                   [&::-webkit-scrollbar-track]:bg-stone-200/50 dark:[&::-webkit-scrollbar-track]:bg-stone-800/60
                   [&::-webkit-scrollbar-thumb]:bg-stone-400/70 dark:[&::-webkit-scrollbar-thumb]:bg-stone-600/80
                   [&::-webkit-scrollbar-thumb]:rounded-full
                   hover:[&::-webkit-scrollbar-thumb]:bg-[#6F4E37] dark:hover:[&::-webkit-scrollbar-thumb]:bg-amber-500
                   transition-colors text-xs">
                <span class="text-stone-400 font-semibold shrink-0 mr-1 text-[11px]">Lompat:</span>
                @foreach($steps as $s)
                    <a href="#langkah-{{ $s['number'] }}"
                       class="shrink-0 px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-amber-100 hover:text-amber-900 dark:bg-stone-800 dark:hover:bg-cyan-950/60 dark:text-stone-300 dark:hover:text-cyan-300 font-medium transition-colors">
                        #{{ $s['number'] }} {{ Str::limit($s['title'], 18) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ========================================================
         8-STEP DETAILED CARDS WITH MOCKUP SCREENSHOTS
    ======================================================== --}}
    <div class="space-y-8">
        @foreach($steps as $step)
            <div id="langkah-{{ $step['number'] }}"
                 class="scroll-mt-36 p-5 sm:p-8 rounded-3xl bg-white dark:bg-[#131B2A] border border-stone-200/90 dark:border-stone-800 shadow-sm hover:shadow-md transition-shadow">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                    
                    {{-- Left Column: Step Details & SOP Information --}}
                    <div class="lg:col-span-6 space-y-4">
                        {{-- Header Row: Step Number & SOP Badge --}}
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-gradient-to-br {{ $step['number_gradient'] }} flex items-center justify-center font-extrabold text-lg shadow-md shrink-0">
                                {{ sprintf('%02d', $step['number']) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border tracking-wider {{ $step['badge_color'] }}">
                                {{ $step['sop'] }}
                            </span>
                        </div>

                        {{-- Step Title & Summary --}}
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold font-heading text-stone-900 dark:text-white leading-snug">
                                {{ $step['title'] }}
                            </h2>
                            <p class="mt-1 text-sm text-[#6F4E37] dark:text-amber-400 font-medium">
                                {{ $step['summary'] }}
                            </p>
                        </div>

                        {{-- Step-by-Step Instructions List --}}
                        <div class="space-y-2.5 pt-1">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500">
                                Tindakan Yang Harus Dilakukan:
                            </h4>
                            <ul class="space-y-2">
                                @foreach($step['details'] as $index => $detail)
                                    <li class="flex items-start gap-2.5 text-sm text-stone-700 dark:text-stone-300 leading-relaxed">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 dark:bg-cyan-500/10 dark:text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            {{ $index + 1 }}
                                        </span>
                                        <span>{{ $detail }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Pro Tip / Warning Box --}}
                        @if($step['tip'])
                            <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-amber-900 dark:bg-amber-950/20 dark:border-amber-800/60 dark:text-amber-300 text-xs sm:text-sm leading-relaxed flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6 6 0 10-4.5 5.85m4.5-5.85h.008v.008H12v-.008zM12 21a.75.75 0 100-1.5.75.75 0 000 1.5z"/>
                                </svg>
                                <div>
                                    <strong class="font-semibold">Catatan Penting:</strong> {{ $step['tip'] }}
                                </div>
                            </div>
                        @endif

                        {{-- Optional Action Button --}}
                        @if($step['action_label'] && $step['action_url'])
                            <div class="pt-2">
                                <a href="{{ $step['action_url'] }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#6F4E37] text-white hover:bg-[#5a3f2c] dark:bg-cyan-600 dark:hover:bg-cyan-500 transition-colors shadow-sm">
                                    <span>{{ $step['action_label'] }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column: Mockup Screenshot Frame with Lightbox Zoom --}}
                    <div class="lg:col-span-6">
                        <div class="relative group">

                            {{-- ==========================================
                                 DESKTOP BROWSER FRAME MOCKUP
                            =========================================== --}}
                            <div x-show="device === 'desktop'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="rounded-2xl overflow-hidden border border-stone-200 dark:border-stone-700 bg-stone-100 dark:bg-stone-900 shadow-md hover:shadow-xl transition-all duration-300">
                                
                                {{-- Browser Titlebar --}}
                                <div class="px-4 py-2.5 bg-stone-200/80 dark:bg-stone-800/90 border-b border-stone-300/80 dark:border-stone-700 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                    </div>
                                    <div class="flex-1 max-w-sm truncate text-center px-3 py-0.5 rounded-md bg-white/70 dark:bg-stone-900/80 text-[11px] font-mono text-stone-600 dark:text-stone-300 border border-stone-300/50 dark:border-stone-700/50">
                                        sitefa.smkn1bangsri.sch.id/admin
                                    </div>
                                    <span class="text-[10px] uppercase font-bold text-stone-400 dark:text-stone-500">
                                        Desktop
                                    </span>
                                </div>

                                {{-- Image Container (Clickable) --}}
                                <div class="relative overflow-hidden cursor-zoom-in hover:opacity-95 transition bg-stone-950 flex items-center justify-center aspect-[16/10]"
                                     @click="activeZoomImg = '{{ asset('images/guides/admin/' . $step['desktop_img']) }}'; activeZoomTitle = 'Langkah {{ $step['number'] }}: {{ addslashes($step['title']) }}'; zoomModal = true">
                                    <img src="{{ asset('images/guides/admin/' . $step['desktop_img']) }}"
                                         alt="Screenshot {{ $step['title'] }} - Versi Komputer"
                                         class="w-full h-full object-contain group-hover:scale-[1.02] transition-transform duration-300"
                                         loading="lazy">

                                    {{-- Hover Overlay & Zoom Pill --}}
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                        <span class="px-3.5 py-1.5 rounded-full bg-black/75 text-white text-xs font-semibold backdrop-blur-sm flex items-center gap-1.5 shadow-lg">
                                            <svg class="w-4 h-4 text-amber-300 dark:text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                            </svg>
                                            <span>Klik untuk Memperbesar</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- ==========================================
                                 MOBILE SMARTPHONE FRAME MOCKUP
                            =========================================== --}}
                            <div x-show="device === 'mobile'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="max-w-[320px] mx-auto rounded-[2.5rem] p-3 border-4 border-stone-800 dark:border-stone-700 bg-stone-900 shadow-2xl hover:shadow-stone-900/50 transition-all duration-300">
                                
                                {{-- Smartphone Speaker Notch --}}
                                <div class="w-20 h-4 bg-stone-800 rounded-full mx-auto mb-2 flex items-center justify-center">
                                    <div class="w-8 h-1 bg-stone-700 rounded-full"></div>
                                </div>

                                {{-- Image Container (Clickable) --}}
                                <div class="relative rounded-[1.75rem] overflow-hidden cursor-zoom-in hover:opacity-95 transition bg-stone-950 flex items-center justify-center aspect-[9/16]"
                                     @click="activeZoomImg = '{{ asset('images/guides/admin/' . $step['mobile_img']) }}'; activeZoomTitle = 'Langkah {{ $step['number'] }}: {{ addslashes($step['title']) }}'; zoomModal = true">
                                    <img src="{{ asset('images/guides/admin/' . $step['mobile_img']) }}"
                                         alt="Screenshot {{ $step['title'] }} - Versi Ponsel"
                                         class="w-full h-full object-contain group-hover:scale-[1.02] transition-transform duration-300"
                                         loading="lazy">

                                    {{-- Hover Overlay & Zoom Pill --}}
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                        <span class="px-3 py-1 rounded-full bg-black/75 text-white text-[11px] font-semibold backdrop-blur-sm flex items-center gap-1.5 shadow-lg">
                                            <svg class="w-3.5 h-3.5 text-amber-300 dark:text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                            </svg>
                                            <span>Perbesar</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Smartphone Bottom Home Indicator --}}
                                <div class="w-28 h-1 bg-stone-700 rounded-full mx-auto mt-2.5"></div>
                            </div>

                            <p class="mt-2 text-center text-xs text-stone-400 dark:text-stone-500">
                                💡 Klik gambar untuk melihat tangkapan layar ukuran penuh
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        @endforeach
    </div>

    {{-- ========================================================
         LIGHTBOX ZOOM MODAL (TELEPORT TO BODY)
    ======================================================== --}}
    <template x-teleport="body">
        <div x-show="zoomModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="zoomModal = false"
             class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 bg-black/85 backdrop-blur-md"
             style="display: none;"
             @click.self="zoomModal = false"
             x-cloak>

            {{-- Modal Dialog Container --}}
            <div x-show="zoomModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-[110] max-w-5xl w-full max-h-[92vh] flex flex-col bg-stone-900 border border-stone-700 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl">

                {{-- Modal Topbar Header --}}
                <div class="px-4 sm:px-6 py-3.5 bg-stone-800/95 border-b border-stone-700 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0">
                            Pratinjau SOP Admin
                        </span>
                        <h3 class="text-sm sm:text-base font-bold text-white truncate font-heading"
                            x-text="activeZoomTitle"></h3>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[11px] text-stone-400 hidden sm:inline-block">
                            Tekan <kbd class="px-1.5 py-0.5 rounded bg-stone-700 text-stone-200 font-mono text-[10px]">ESC</kbd> untuk menutup
                        </span>
                        <button type="button"
                                @click="zoomModal = false"
                                class="p-1.5 rounded-xl bg-stone-700/60 hover:bg-stone-700 text-stone-300 hover:text-white transition-colors cursor-pointer"
                                title="Tutup (ESC)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Image Viewport --}}
                <div class="flex-1 overflow-auto p-2 sm:p-4 bg-stone-950 flex items-center justify-center min-h-[250px]">
                    <img :src="activeZoomImg"
                         :alt="activeZoomTitle"
                         class="max-w-full max-h-[78vh] object-contain rounded-lg shadow-lg">
                </div>

                {{-- Modal Footer --}}
                <div class="px-4 py-2.5 bg-stone-900 border-t border-stone-800 flex items-center justify-between text-xs text-stone-400">
                    <span>Tangkapan Layar Panduan SOP Operasional Admin SITEFA</span>
                    <button type="button"
                            @click="zoomModal = false"
                            class="hover:text-white font-medium underline cursor-pointer">
                        Tutup Pratinjau
                    </button>
                </div>

            </div>
        </div>
    </template>

    {{-- ========================================================
         FAQ KHUSUS SOP ADMIN (FULL-WIDTH GRID SIMETRIS)
    ======================================================== --}}
    <div class="w-full p-6 sm:p-8 rounded-3xl bg-[#FAF8F5] dark:bg-[#0E1420] border border-stone-200 dark:border-stone-800">
        <div class="w-full space-y-4">
            <h3 class="text-lg font-bold font-heading text-stone-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
                <span>Pertanyaan Umum Seputar SOP Admin &amp; Super Admin (FAQ)</span>
            </h3>

            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-stone-600 dark:text-stone-300">
                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Berapa lama batas waktu hold unit sebelum otomatis dibatalkan?</h4>
                    <p class="leading-relaxed">Unit ditahan selama <strong>10 menit</strong> sejak permohonan diajukan oleh siswa atau guru. Jika dalam rentang 10 menit peminjam belum hadir di ruang lab untuk proses serah terima fisik bersama petugas/teknisi Mas Donny, sistem otomatis membatalkan pemesanan agar unit kembali tersedia di katalog publik.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Kapan harus memilih kondisi Rusak Berat saat verifikasi pengembalian?</h4>
                    <p class="leading-relaxed">Pilih opsi <strong>Rusak Berat</strong> apabila unit mengalami kerusakan fungsional fatal, pecah komponen vital, atau membutuhkan servis perbaikan teknisi luar. Unit bersangkutan akan otomatis dialihkan ke status <em>Karantina / Maintenance</em> sehingga tidak dapat dipinjam kembali hingga tuntas diservis.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Siapa saja yang berhak melakukan sinkronisasi SiPintu?</h4>
                    <p class="leading-relaxed">Fitur integrasi sinkronisasi Gateway SiPintu dan menu Audit Log forensik bersifat <strong>eksklusif hanya untuk Super Admin</strong>. Admin operasional berfokus pada monitoring permohonan, aksi persetujuan, pendampingan kamera serah terima, serta pengelolaan master 31 unit aset inventaris.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Bagaimana jika peminjam tidak memiliki nomor WA di SiPintu?</h4>
                    <p class="leading-relaxed">Sistem SITEFA secara otomatis meminta pengisian nomor WhatsApp aktif pada langkah onboarding pertama kali. Pengguna juga dapat memperbarui nomor telepon secara mandiri melalui menu Pengaturan Profil kapan saja agar notifikasi dan pintasan komunikasi admin tetap berjalan lancar.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
