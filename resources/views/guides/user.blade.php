@extends('layouts.app')

@section('title', 'Panduan Peminjaman - SITEFA SMKN 1 Bangsri')

@section('content')
@php
$steps = [
    [
        'number' => 1,
        'slug' => 'dashboard-cta',
        'sop' => 'SOP-01 · INISIASI',
        'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
        'number_gradient' => 'from-amber-600 to-amber-800 text-white',
        'title' => 'Akses Dashboard & Tombol Mulai Pinjam',
        'summary' => 'Masuk ke sistem SITEFA dan mulai proses permohonan melalui tombol pintas di beranda.',
        'details' => [
            'Buka dan login ke akun SITEFA Anda menggunakan kredensial resmi sekolah atau Single Sign-On (SiPintu).',
            'Pada halaman utama Dashboard, periksa ringkasan status alat dan riwayat peminjaman Anda.',
            'Klik tombol CTA utama "Mulai Pinjam Sekarang" atau klik menu navigasi "Barang" di sidebar (desktop) atau bottom bar (ponsel).',
        ],
        'tip' => 'Pastikan nomor WhatsApp pada Profil Anda sudah benar dan aktif agar dapat menerima pesan notifikasi otomatis saat pengajuan disetujui.',
        'desktop_img' => 'step-1-dashboard-cta.png',
        'mobile_img' => 'step-1-dashboard-cta-mobile.png',
        'action_label' => 'Buka Dashboard',
        'action_url' => route('dashboard'),
    ],
    [
        'number' => 2,
        'slug' => 'katalog-tersedia',
        'sop' => 'SOP-02 · KATALOG ASET',
        'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
        'number_gradient' => 'from-emerald-600 to-emerald-800 text-white',
        'title' => 'Pilih Unit di Katalog Barang',
        'summary' => 'Jelajahi daftar peralatan lab TEFA dan temukan barang yang berstatus "Tersedia".',
        'details' => [
            'Buka halaman Katalog Barang untuk meninjau seluruh koleksi inventaris lab TEFA.',
            'Gunakan fitur pencarian nama alat atau filter berdasarkan kategori (Kamera, Komputer, Audio Visual, Jaringan, dan Peralatan Lab).',
            'Pastikan kartu barang menampilkan badge status berwarna hijau bertuliskan "Tersedia".',
            'Klik tombol "Pinjam Barang" pada kartu barang yang ingin Anda gunakan.',
        ],
        'tip' => 'Periksa detail spesifikasi dan kondisi unit pada kartu barang sebelum mengajukan permohonan.',
        'desktop_img' => 'step-2-katalog-tersedia.png',
        'mobile_img' => 'step-2-katalog-tersedia-mobile.png',
        'action_label' => 'Buka Katalog Barang',
        'action_url' => route('assets.index'),
    ],
    [
        'number' => 3,
        'slug' => 'form-pengajuan',
        'sop' => 'SOP-03 · FORMULIR PINJAM',
        'badge_color' => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800',
        'number_gradient' => 'from-sky-600 to-sky-800 text-white',
        'title' => 'Pengisian Formulir & Tenggat Waktu',
        'summary' => 'Tentukan waktu pengembalian dan tuliskan keperluan peminjaman secara transparan.',
        'details' => [
            'Pilih tanggal dan estimasi jam rencana pengembalian unit barang.',
            'Tuliskan keperluan peminjaman secara jelas (misalnya: Praktik Mata Pelajaran Kejuruan, Pembuatan Proyek TEFA, Kegiatan Pembelajaran di Kelas).',
            'Periksa ringkasan data sebelum mengirimkan permohonan.',
            'Klik tombol "Ajukan Peminjaman" untuk meneruskan tiket ke petugas lab.',
        ],
        'tip' => 'Bagi Guru yang meminjam untuk kepentingan KBM resmi, sistem SITEFA memberikan prioritas penjadwalan secara otomatis.',
        'desktop_img' => 'step-3-form-pengajuan.png',
        'mobile_img' => 'step-3-form-pengajuan-mobile.png',
        'action_label' => null,
        'action_url' => null,
    ],
    [
        'number' => 4,
        'slug' => 'countdown-reservasi',
        'sop' => 'SOP-04 · HOLDING TIMER',
        'badge_color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
        'number_gradient' => 'from-rose-600 to-rose-800 text-white',
        'title' => 'Menunggu Serah Terima (Timer 10 Menit Mas Donny)',
        'summary' => 'Sistem mengunci unit dan menjalankan hitung mundur 10 menit bagi peminjam untuk hadir di lab.',
        'details' => [
            'Setelah permohonan terkirim, status berubah menjadi "Menunggu Persetujuan / Reservasi".',
            'Sistem mengunci unit aset dengan hitung mundur (Countdown Timer) selama 10 menit agar tidak dipinjam pengguna lain.',
            'Segera melangkah ke ruang Lab RPL untuk menemui Pengelola Mas Donny guna verifikasi peminjaman secara langsung.',
        ],
        'tip' => 'PENTING: Jangan terlambat! Jika waktu 10 menit habis tanpa ada konfirmasi serah terima fisik di lab, reservasi otomatis dibatalkan sistem (auto-cancel).',
        'desktop_img' => 'step-4-countdown-reservasi.png',
        'mobile_img' => 'step-4-countdown-reservasi-mobile.png',
        'action_label' => null,
        'action_url' => null,
    ],
    [
        'number' => 5,
        'slug' => 'status-approved',
        'sop' => 'SOP-05 · PERSETUJUAN RESMI',
        'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
        'number_gradient' => 'from-amber-500 to-amber-700 text-white',
        'title' => 'Persetujuan Admin & Tombol Ambil Barang',
        'summary' => 'Petugas lab memverifikasi permohonan dan membuka akses serah terima unit di aplikasi.',
        'details' => [
            'Admin lab memeriksa kesiapan fisik peralatan dan kelengkapannya di lab TEFA.',
            'Ketika Admin menyetujui, status transaksi di layar Anda langsung diperbarui menjadi "Disetujui" (Approved).',
            'Notifikasi WhatsApp otomatis terkirim ke ponsel Anda sebagai bukti persetujuan.',
            'Tombol "Ambil Barang / Serah Terima" di aplikasi Anda akan aktif untuk proses dokumentasi kamera.',
        ],
        'tip' => 'Pastikan Anda telah berada di hadapan petugas lab sebelum melanjutkan ke langkah dokumentasi serah terima.',
        'desktop_img' => 'step-5-status-approved.png',
        'mobile_img' => 'step-5-status-approved-mobile.png',
        'action_label' => null,
        'action_url' => null,
    ],
    [
        'number' => 6,
        'slug' => 'kamera-serah-terima',
        'sop' => 'SOP-06 · BUKTI SERAH TERIMA',
        'badge_color' => 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
        'number_gradient' => 'from-teal-600 to-teal-800 text-white',
        'title' => 'Pemotretan Kamera Serah Terima di Lab',
        'summary' => 'Dokumentasi bukti fisik kondisi awal barang sebelum dibawa keluar dari lab TEFA.',
        'details' => [
            'Buka fitur kamera interaktif yang terpasang langsung di antarmuka SITEFA.',
            'Arahkan kamera ke barang beserta kelengkapannya yang diserahkan oleh petugas lab.',
            'Ambil foto kondisi fisik barang secara jelas di hadapan Mas Donny.',
            'Konfirmasi pengambilan barang dan unit inventaris resmi berpindah ke tanggung jawab Anda.',
        ],
        'tip' => 'Dokumentasi foto ini merupakan bukti perlindungan bersama yang menjamin kondisi awal barang saat pertama kali diterima.',
        'desktop_img' => 'step-6-kamera-serah-terima.png',
        'mobile_img' => 'step-6-kamera-serah-terima-mobile.png',
        'action_label' => null,
        'action_url' => null,
    ],
    [
        'number' => 7,
        'slug' => 'sedang-dipinjam',
        'sop' => 'SOP-07 · PERIODE PEMAKAIAN',
        'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800',
        'number_gradient' => 'from-indigo-600 to-indigo-800 text-white',
        'title' => 'Penggunaan Unit & Menu Peminjaman Saya',
        'summary' => 'Pantau masa aktif peminjaman dan rawat peralatan dengan penuh tanggung jawab.',
        'details' => [
            'Status peminjaman kini aktif berstatus "Sedang Dipinjam" (Active Borrowing).',
            'Pantau sisa durasi dan batas waktu pengembalian kapan saja di menu "Peminjaman Saya".',
            'Gunakan unit alat sesuai SOP keselamatan kerja lab TEFA SMKN 1 Bangsri.',
            'Admin akan mengirimkan pesan pengingat WhatsApp ketika waktu pengembalian mendekati batas waktu.',
        ],
        'tip' => 'Jaga kebersihan alat dan simpan di tempat yang aman selama masa peminjaman berlangsung.',
        'desktop_img' => 'step-7-sedang-dipinjam.png',
        'mobile_img' => 'step-7-sedang-dipinjam-mobile.png',
        'action_label' => 'Lihat Peminjaman Saya',
        'action_url' => route('borrowings.mine'),
    ],
    [
        'number' => 8,
        'slug' => 'kamera-pengembalian',
        'sop' => 'SOP-08 · PENGAJUAN KEMBALI',
        'badge_color' => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800',
        'number_gradient' => 'from-orange-600 to-orange-800 text-white',
        'title' => 'Pemotretan Kamera Pengembalian Unit',
        'summary' => 'Bawa barang kembali ke Lab TEFA dan ambil foto bukti kondisi pengembalian.',
        'details' => [
            'Bawa kembali peralatan beserta seluruh kelengkapannya (charger contohnya) ke Lab TEFA tepat waktu.',
            'Buka menu "Peminjaman Saya" lalu klik tombol "Kembalikan Barang".',
            'Aktifkan kamera untuk memotret kondisi fisik barang saat diserahkan di meja petugas lab.',
            'Kirim pengajuan pengembalian untuk diverifikasi oleh Mas Donny.',
        ],
        'tip' => 'Pastikan semua kelengkapan terpasang rapi dan bersih seperti kondisi awal saat Anda menerima barang.',
        'desktop_img' => 'step-8-kamera-pengembalian.png',
        'mobile_img' => 'step-8-kamera-pengembalian-mobile.png',
        'action_label' => null,
        'action_url' => null,
    ],
    [
        'number' => 9,
        'slug' => 'menunggu-verifikasi',
        'sop' => 'SOP-09 · VERIFIKASI & CLOSING',
        'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
        'number_gradient' => 'from-emerald-600 to-emerald-900 text-white',
        'title' => 'Verifikasi Akhir oleh Admin',
        'summary' => 'Pemeriksaan fisik oleh pengelola lab hingga transaksi ditutup secara tuntas.',
        'details' => [
            'Status berubah menjadi "Menunggu Verifikasi Pengembalian" saat petugas memeriksa fisik dan fungsi alat.',
            'Admin lab memeriksa kebersihan, keutuhan fisik, dan fungsi peralatan secara cermat.',
            'Setelah admin mengonfirmasi tombol verifikasi di panel monitoring, transaksi resmi berstatus "Selesai" (Returned).',
            'Akun Anda terbebas dari tanggungan inventaris dan siap mengajukan peminjaman berikutnya!',
        ],
        'tip' => 'Ketertiban Anda dalam merawat dan mengembalikan barang tepat waktu tercatat dalam audit log sebagai reputasi peminjam teladan.',
        'desktop_img' => 'step-9-menunggu-verifikasi.png',
        'mobile_img' => 'step-9-menunggu-verifikasi-mobile.png',
        'action_label' => 'Lihat Riwayat Selesai',
        'action_url' => route('borrowings.mine'),
    ],
];
@endphp

<div x-data="{
        device: 'desktop',
        deviceMode: 'desktop',
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>SOP RESMI INVENTARIS TEFA SMKN 1 BANGSRI</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-heading text-white tracking-tight leading-tight">
                    Panduan Alur Peminjaman Alat &amp; Barang
                </h1>

                <p class="text-sm sm:text-base text-stone-200/90 dark:text-stone-300 leading-relaxed">
                    Pelajari <strong class="text-amber-300 dark:text-cyan-300 font-semibold">9 langkah terstruktur</strong> peminjaman unit alat lab TEFA. Dilengkapi simulasi tangkapan layar antarmuka asli untuk pengguna komputer dan ponsel pintar.
                </p>

                {{-- Fast Metric Badges --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">9 Langkah</div>
                        <div class="text-stone-300 text-[11px]">Alur SOP Lengkap</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">10 Menit</div>
                        <div class="text-stone-300 text-[11px]">Holding Timer Lab</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">Kamera Web</div>
                        <div class="text-stone-300 text-[11px]">Bukti Fisik Transparan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 dark:border-stone-800">
                        <div class="font-bold text-amber-300 dark:text-cyan-300 text-sm">WhatsApp</div>
                        <div class="text-stone-300 text-[11px]">Pemberitahuan Otomatis</div>
                    </div>
                </div>
            </div>

            {{-- Quick action card --}}
            <div class="shrink-0 flex flex-col sm:flex-row lg:flex-col gap-3">
                <a href="{{ route('assets.index') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl font-semibold text-sm bg-gradient-to-r from-amber-400 to-[#C89B3C] text-[#30251F] hover:from-amber-300 hover:to-amber-400 dark:from-cyan-400 dark:to-cyan-500 dark:text-stone-950 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-95 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0H3.75M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125V6c0-.621-.504-1.125-1.125-1.125H3.375C2.754 4.875 2.25 5.379 2.25 6v.375c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span>Mulai Cari Barang</span>
                </a>

                <a href="{{ route('borrowings.mine') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl font-semibold text-sm bg-white/10 hover:bg-white/20 text-white dark:bg-stone-800/80 dark:hover:bg-stone-800 border border-white/20 dark:border-stone-700 transition-all duration-200 active:scale-95 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                    <span>Status Peminjaman Saya</span>
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
                            @click="device = 'desktop'; deviceMode = 'desktop'"
                            :class="(device === 'desktop' || deviceMode === 'desktop') 
                                ? 'bg-white dark:bg-[#131B2A] text-[#6F4E37] dark:text-cyan-400 shadow-sm font-bold border border-stone-200/80 dark:border-cyan-500/30' 
                                : 'text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 font-medium'"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs sm:text-sm transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                        </svg>
                        <span>Mode Komputer / Desktop</span>
                    </button>

                    {{-- Tab 2: Ponsel / Mobile --}}
                    <button type="button"
                            @click="device = 'mobile'; deviceMode = 'mobile'"
                            :class="(device === 'mobile' || deviceMode === 'mobile') 
                                ? 'bg-white dark:bg-[#131B2A] text-[#6F4E37] dark:text-cyan-400 shadow-sm font-bold border border-stone-200/80 dark:border-cyan-500/30' 
                                : 'text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 font-medium'"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs sm:text-sm transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                        </svg>
                        <span>Mode Ponsel / HP</span>
                    </button>
                </div>
            </div>

            {{-- Info indicator --}}
            <div class="text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5 self-end md:self-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Menampilkan tangkapan layar: <strong class="text-stone-800 dark:text-stone-200" x-text="(device === 'desktop' || deviceMode === 'desktop') ? 'Versi Desktop / Layar Lebar' : 'Versi Mobile / Ponsel'"></strong></span>
            </div>
        </div>

        {{-- Step Quick-Jump Pills --}}
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
         9-STEP DETAILED CARDS WITH MOCKUP SCREENSHOTS
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
                            <div x-show="device === 'desktop' || deviceMode === 'desktop'" 
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
                                        sitefa.smkn1bangsri.sch.id
                                    </div>
                                    <span class="text-[10px] uppercase font-bold text-stone-400 dark:text-stone-500">
                                        Desktop
                                    </span>
                                </div>

                                {{-- Image Container (Clickable) --}}
                                <div class="relative overflow-hidden cursor-zoom-in hover:opacity-95 transition bg-stone-950 flex items-center justify-center aspect-[16/10]"
                                     @click="activeZoomImg = '{{ asset('images/guides/user/' . $step['desktop_img']) }}'; activeZoomTitle = 'Langkah {{ $step['number'] }}: {{ addslashes($step['title']) }}'; zoomModal = true">
                                    <img src="{{ asset('images/guides/user/' . $step['desktop_img']) }}"
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
                            <div x-show="device === 'mobile' || deviceMode === 'mobile'" 
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
                                     @click="activeZoomImg = '{{ asset('images/guides/user/' . $step['mobile_img']) }}'; activeZoomTitle = 'Langkah {{ $step['number'] }}: {{ addslashes($step['title']) }}'; zoomModal = true">
                                    <img src="{{ asset('images/guides/user/' . $step['mobile_img']) }}"
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
                            Pratinjau Foto
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
                    <span>Tangkapan Layar Panduan Peminjam SITEFA</span>
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
         FAQ & BANTUAN LAB
    ======================================================== --}}
    <div class="w-full p-6 sm:p-8 rounded-3xl bg-[#FAF8F5] dark:bg-[#0E1420] border border-stone-200 dark:border-stone-800">
        <div class="w-full space-y-4">
            <h3 class="text-lg font-bold font-heading text-stone-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
                <span>Pertanyaan Umum Seputar Peminjaman (FAQ)</span>
            </h3>

            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-stone-600 dark:text-stone-300">
                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Berapa lama batas waktu reservasi?</h4>
                    <p class="leading-relaxed">Unit ditahan selama <strong>10 menit</strong> sejak pengajuan terkirim. Jika dalam 10 menit Anda belum hadir di lab, sistem otomatis membatalkan pemesanan.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Mengapa pemotretan kamera wajib?</h4>
                    <p class="leading-relaxed">Dokumentasi kamera menjadi bukti otentik kondisi fisik awal dan akhir barang agar peminjam dan pihak sekolah memiliki catatan kondisi yang jelas dan adil.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Apakah Guru mendapatkan prioritas?</h4>
                    <p class="leading-relaxed">Ya, pengajuan oleh Bapak/Ibu Guru untuk kebutuhan pembelajaran langsung diprioritaskan oleh sistem monitoring antrean lab.</p>
                </div>

                <div class="w-full h-full flex flex-col justify-start p-5 rounded-2xl bg-white/70 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800 shadow-sm space-y-1.5">
                    <h4 class="font-bold text-stone-800 dark:text-stone-100">Bagaimana jika terlambat mengembalikan?</h4>
                    <p class="leading-relaxed">Sistem akan menandai status sebagai terlambat (*overdue*) dan mengirimkan pengingat WhatsApp. Keterlambatan berulang dapat memengaruhi izin peminjaman selanjutnya.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
