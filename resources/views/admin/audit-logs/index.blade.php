@extends('layouts.app')

@section('title', 'Audit Log Sistem | TE-Vault')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8" x-data="{ selectedLog: null }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-bold text-gray-800">
                    Audit Log Sistem
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    Super Admin
                </span>
            </div>
            <p class="text-gray-500 mt-1">
                Catatan riwayat aktivitas, perubahan data, dan keamanan sistem inventaris.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Append-Only Immutable Logs</span>
            </span>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            
            {{-- Search Bar --}}
            <div class="sm:col-span-8">
                <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">
                    Pencarian Aktor, Aksi, atau Entitas
                </label>
                <div class="relative">
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama user, email, aksi (cth: borrowing.returned)..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-gray-300 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Action Filter --}}
            <div class="sm:col-span-4 flex items-end gap-2">
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-[#6F4E37] text-white text-xs font-bold hover:bg-[#5a3f2c] transition shadow-sm"
                >
                    Filter Data
                </button>

                @if(request()->hasAny(['search', 'action']))
                    <a
                        href="{{ route('admin.audit-logs.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-gray-600 text-xs font-medium hover:bg-gray-100 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Audit Log Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Waktu</th>
                        <th class="px-5 py-3.5">Aktor / Pengguna</th>
                        <th class="px-5 py-3.5">Aksi / Event</th>
                        <th class="px-5 py-3.5">Entitas Terkait</th>
                        <th class="px-5 py-3.5">Perubahan Data</th>
                        <th class="px-5 py-3.5">IP &amp; Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php
                            $actionName = $log->action;
                            $actionBadge = match(true) {
                                str_contains($actionName, 'borrowing.requested') => ['bg-blue-50 text-blue-700 border-blue-200', 'Pengajuan Pinjam'],
                                str_contains($actionName, 'borrowing.returned') => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Pengembalian'],
                                str_contains($actionName, 'borrowing.approved') => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'Persetujuan'],
                                str_contains($actionName, 'borrowing.rejected') => ['bg-rose-50 text-rose-700 border-rose-200', 'Penolakan'],
                                str_contains($actionName, 'created') => ['bg-green-50 text-green-700 border-green-200', 'Buat Data'],
                                str_contains($actionName, 'updated') => ['bg-amber-50 text-amber-700 border-amber-200', 'Ubah Data'],
                                str_contains($actionName, 'deleted') => ['bg-rose-50 text-rose-700 border-rose-200', 'Hapus Data'],
                                default => ['bg-gray-100 text-gray-700 border-gray-200', $actionName],
                            };
                        @endphp
                        <tr class="hover:bg-amber-50/20 transition">
                            
                            {{-- Waktu --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Aktor --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($log->actor)
                                    <p class="font-bold text-gray-800">{{ $log->actor->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $log->actor->email }}</p>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">
                                        Sistem / Otomatis
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full font-bold border text-[11px] {{ $actionBadge[0] }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Entitas --}}
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-xs">
                                <span class="text-gray-700 font-medium">
                                    {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </span>
                            </td>

                            {{-- Perubahan Data --}}
                            <td class="px-5 py-4">
                                @if($log->new_values || $log->old_values)
                                    <button
                                        type="button"
                                        @click="selectedLog = {{ json_encode([
                                            'id' => $log->id,
                                            'action' => $log->action,
                                            'entity' => class_basename($log->entity_type) . ' #' . $log->entity_id,
                                            'old' => $log->old_values,
                                            'new' => $log->new_values,
                                            'meta' => $log->metadata,
                                        ]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-semibold transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Diff (JSON)
                                    </button>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            {{-- IP & Device Metadata --}}
                            <td class="px-5 py-4 text-[11px]">
                                @if(!empty($log->metadata['ip_address']))
                                    <p class="font-mono text-gray-700 font-semibold">{{ $log->metadata['ip_address'] }}</p>
                                @endif
                                @if(!empty($log->metadata['user_agent']))
                                    <p class="text-[10px] text-gray-400 line-clamp-1 max-w-[200px]" title="{{ $log->metadata['user_agent'] }}">
                                        {{ $log->metadata['user_agent'] }}
                                    </p>
                                @endif
                                @if(empty($log->metadata['ip_address']) && empty($log->metadata['user_agent']))
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Belum ada catatan audit log yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $logs->links() }}
            </div>
        @endif

    </div>

    {{-- Modal Preview Detail Audit Log (JSON Diff) --}}
    <div
        x-show="selectedLog !== null"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak
        x-transition
    >
        <div
            class="max-w-2xl w-full mx-auto bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
            @click.away="selectedLog = null"
        >
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Detail Perubahan Audit #<span x-text="selectedLog?.id"></span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="selectedLog?.action + ' pada ' + selectedLog?.entity"></p>
                </div>
                <button type="button" @click="selectedLog = null" class="text-gray-400 hover:text-gray-600 font-bold p-1 text-lg">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <template x-if="selectedLog?.old">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Nilai Sebelum (Old Values)
                        </h4>
                        <pre class="p-3 bg-gray-900 text-rose-300 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.old, null, 2)"></pre>
                    </div>
                </template>

                <template x-if="selectedLog?.new">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Nilai Sesudah (New Values)
                        </h4>
                        <pre class="p-3 bg-gray-900 text-emerald-300 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.new, null, 2)"></pre>
                    </div>
                </template>

                <template x-if="selectedLog?.meta && Object.keys(selectedLog?.meta).length > 0">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-blue-700 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Metadata &amp; Header
                        </h4>
                        <pre class="p-3 bg-gray-900 text-blue-200 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.meta, null, 2)"></pre>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button
                    type="button"
                    @click="selectedLog = null"
                    class="px-4 py-2 rounded-xl bg-gray-700 text-white text-xs font-semibold hover:bg-gray-800 transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@endsection
