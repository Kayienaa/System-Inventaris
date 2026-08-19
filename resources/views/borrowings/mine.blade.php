@extends('layouts.app')

@section('title', 'Peminjaman Saya | TE-Vault')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Peminjaman Saya</h1>
            <p class="text-gray-500 mt-1">Riwayat & status peminjaman barang kamu</p>
        </div>

        @if ($borrowings->count())

            <div class="space-y-4">
                @foreach ($borrowings as $borrowing)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center justify-between gap-4">

                        <div>
                            <p class="font-semibold text-gray-800">{{ $borrowing->asset->name }}</p>
                            <p class="text-sm text-gray-500">{{ $borrowing->asset->asset_code }}</p>

                            @if ($borrowing->due_at)
                                <p class="text-xs text-gray-400 mt-1">
                                    Jatuh tempo: {{ $borrowing->due_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>

                        <div>
                            @php
                                $statusLabel = match ($borrowing->status->value ?? $borrowing->status) {
                                    'requested' => ['Menunggu Persetujuan', 'bg-yellow-100 text-yellow-700'],
                                    'approved' => ['Disetujui', 'bg-blue-100 text-blue-700'],
                                    'borrowed' => ['Dipinjam', 'bg-red-100 text-red-700'],
                                    'return_pending_verification' => ['Menunggu Verifikasi', 'bg-purple-100 text-purple-700'],
                                    'returned' => ['Selesai', 'bg-green-100 text-green-700'],
                                    'rejected' => ['Ditolak', 'bg-gray-100 text-gray-600'],
                                    'cancelled' => ['Dibatalkan', 'bg-gray-100 text-gray-600'],
                                    default => [ucfirst((string) $borrowing->status), 'bg-gray-100 text-gray-600'],
                                };
                            @endphp

                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusLabel[1] }}">
                                {{ $statusLabel[0] }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $borrowings->links() }}
            </div>

        @else

            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <p class="text-gray-500">Kamu belum pernah meminjam barang.</p>
            </div>

        @endif

    </div>

@endsection
