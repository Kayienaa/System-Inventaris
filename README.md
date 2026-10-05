## 🔄 Alur & Langkah Eksekusi Peminjaman (Dual-Step Verification)

Sistem SITEFA menerapkan mekanisme verifikasi dua tahap (*Dual-Step Handover*) guna mencegah peminjaman ganda (*double-booking*) dan menjamin akuntabilitas fisik unit secara langsung:

### 1. Masuk & Pengecekan Kontak (Onboarding)
1. **Autentikasi**: Pengguna (siswa menggunakan NIS/email sekolah, guru menggunakan NIP) masuk melalui portal login tunggal.
2. **Validasi WhatsApp**: Pengguna baru wajib melengkapi nomor WhatsApp aktif (`+628...`) pada halaman `/complete-phone` agar notifikasi dan penagihan keterlambatan (*overdue*) dapat terkirim presisi.

### 2. Pengajuan Peminjaman — Tahap 1 (Reservasi & Penguncian Unit)
1. **Pilih Unit di Katalog**: Pengguna membuka menu **Katalog Barang** (aset berstatus *Tersedia* tampil dalam visual *grayscale*).
2. **Formulir Pengajuan**: Klik tombol **"Pinjam Barang"** pada unit yang diinginkan, pilih durasi pengembalian (H+1 s.d. H+3), dan isi catatan keperluan.
3. **Penguncian Otomatis**: Setelah dikirim (*submit*), transaksi berstatus `Pending` dan status aset di database otomatis terkunci menjadi `Dipesan` agar tidak dapat diserobot oleh pengguna lain.

### 3. Persetujuan Admin Operasional (Mas Donny)
1. Admin Operasional membuka menu **Monitoring Peminjaman** (`/admin/borrowings`).
2. Admin meninjau permohonan yang berstatus `Pending`, lalu menekan tombol **"Setujui (Approve)"**.
3. Status transaksi berubah menjadi `Approved`, dan peminjam menerima instruksi untuk mengambil perangkat secara langsung ke lab TEFA.

### 4. Serah Terima Fisik & Bukti Kamera Real-Time — Tahap 2 (Peminjaman Aktif)
1. **Pertemuan di Lab TEFA**: Peminjam datang langsung menemui Admin (Mas Donny) di ruang TEFA SMKN 1 Bangsri.
2. **Pengambilan Foto Serah Terima**: Peminjam membuka menu **Peminjaman Saya** (`/borrowings/mine`), klik **"Ambil Barang / Serah Terima"**, dan mengaktifkan modul kamera *real-time* (tanpa opsi unggah galeri).
3. **Watermarking Otomatis**: Peminjam mengambil foto fisik serah terima bersama Admin. Sistem secara otomatis menyematkan cap *watermark* digital (Nama Peminjam, Jam, dan Tanggal WIB) serta mengompres ukuran foto di bawah 200 KB.
4. **Peminjaman Sah**:
   - Status transaksi resmi berganti menjadi `Borrowed` (*Dipinjam*).
   - Kartu unit di katalog otomatis berubah menjadi berwarna penuh (*full color*) disertai label nama peminjam aktif.
   - Jam batas tenggat pengembalian (*due date*) mulai berjalan.
