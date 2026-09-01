<x-mail::message>
# Pengingat Pengembalian Alat

Halo **{{ $borrowing->borrower->name }}**,

Alat berikut harus segera dikembalikan ke Ruang TEFA:

<x-mail::panel>
**Nama Barang:** {{ $borrowing->asset?->name ?? $borrowing->item?->nama_barang ?? '-' }}<br>
**Kode Aset:** {{ $borrowing->asset?->asset_code ?? $borrowing->item?->kode_unik ?? '-' }}<br>
**Batas Pengembalian:** {{ $borrowing->due_at->format('d M Y, H:i') }} WIB
</x-mail::panel>

Mohon kembalikan tepat waktu untuk menghindari status keterlambatan.

Terima kasih,<br>
Tim TEFA
</x-mail::message>