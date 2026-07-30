<x-mail::message>
# Pengingat Pengembalian Alat

Halo **{{ $borrowing->user->name }}**,

Alat berikut harus segera dikembalikan ke Ruang TEFA:

<x-mail::panel>php artisan make:command SendBorrowingDueReminders
**Nama Barang:** {{ $borrowing->item->nama_barang }}<br>
**Kode Aset:** {{ $borrowing->item->kode_unik }}<br>
**Batas Pengembalian:** {{ $borrowing->tgl_kembali_rencana->format('d M Y, H:i') }} WIB
</x-mail::panel>

Mohon kembalikan tepat waktu untuk menghindari status keterlambatan.

Terima kasih,<br>
Tim TEFA
</x-mail::message>