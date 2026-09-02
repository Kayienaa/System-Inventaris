<x-mail::message>
# Notifikasi Peminjaman Aset

Halo **{{ $borrowing->borrower->name }}**,

Status peminjaman aset Anda telah diperbarui:

<x-mail::panel>
**Aset:** {{ $borrowing->asset?->name ?? '-' }}<br>
**Kode Aset:** {{ $borrowing->asset?->asset_code ?? '-' }}<br>
**Status:** {{ ucfirst($borrowing->status->value ?? (string) $borrowing->status) }}<br>
@if ($borrowing->due_at)
**Batas Pengembalian:** {{ $borrowing->due_at->format('d M Y, H:i') }} WIB<br>
@endif
@if ($borrowing->approval_note)
**Catatan Admin:** {{ $borrowing->approval_note }}<br>
@endif
@if ($borrowing->rejection_reason)
**Alasan Penolakan:** {{ $borrowing->rejection_reason }}<br>
@endif
</x-mail::panel>

Terima kasih,<br>
Tim TEFA SMKN 1 Bangsri
</x-mail::message>
