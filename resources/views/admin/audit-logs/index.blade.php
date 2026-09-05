@extends('layouts.app')

@section('title', 'Audit Log Sistem | TE-Vault')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8" x-data="{ selectedLog: null }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-bold text-stone-900 dark:text-stone-100">
                    Audit Log Sistem
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                    Super Admin
                </span>
            </div>
            <p class="text-stone-500 dark:text-stone-400 mt-1">
                Catatan riwayat aktivitas, perubahan data, dan keamanan sistem inventaris.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('admin.borrowings.export-excel') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Excel
            </a>

            <a
                href="{{ route('admin.borrowings.export-pdf') }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white text-xs font-semibold shadow-sm transition active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Ekspor PDF
            </a>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white dark:bg-[#131B2A] rounded-2xl shadow-sm border border-stone-200 dark:border-stone-800 p-5 mb-6">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            
            {{-- Search Bar --}}
            <div class="sm:col-span-8">
                <label for="search" class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1">
                    Pencarian Aktor, Aksi, atau Entitas
                </label>
                <div class="relative">
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama user, email, aksi (cth: borrowing.returned)..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:border-amber-600 focus:ring-1 focus:ring-amber-600 shadow-sm"
                    >
                    <svg class="w-4 h-4 text-stone-400 dark:text-stone-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Action Filter --}}
            <div class="sm:col-span-4 flex items-end gap-2">
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-[#6F4E37] text-white text-xs font-bold hover:bg-[#5a3f2c] transition shadow-sm active:scale-95"
                >
                    Filter Data
                </button>

                @if(request()->hasAny(['search', 'action']))
                    <a
                        href="{{ route('admin.audit-logs.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-600 dark:text-stone-300 text-xs font-medium hover:bg-stone-100 dark:hover:bg-stone-700 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Audit Log Table Card --}}
    <div class="bg-white dark:bg-[#131B2A] rounded-2xl shadow-sm border border-stone-200 dark:border-stone-800 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600 dark:text-stone-300">
                <thead class="bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300 border-b border-stone-800 text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">Waktu</th>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">Aktor / Pengguna</th>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">Aksi / Event</th>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">Entitas Terkait</th>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">Perubahan Data</th>
                        <th class="px-5 py-3.5 bg-stone-900 text-stone-200 dark:bg-[#0E1420] dark:text-stone-300">IP &amp; Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                    @forelse($logs as $log)
                        @php
                            $actionName = $log->action;
                            $actionBadge = match(true) {
                                str_contains($actionName, 'borrowing.requested') => ['bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50', 'Pengajuan Pinjam'],
                                str_contains($actionName, 'borrowing.returned') => ['bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50', 'Pengembalian'],
                                str_contains($actionName, 'borrowing.approved') => ['bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/50', 'Persetujuan'],
                                str_contains($actionName, 'borrowing.rejected') => ['bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50', 'Penolakan'],
                                str_contains($actionName, 'created') => ['bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300 dark:border-green-800/50', 'Buat Data'],
                                str_contains($actionName, 'updated') => ['bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50', 'Ubah Data'],
                                str_contains($actionName, 'deleted') => ['bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50', 'Hapus Data'],
                                default => ['bg-stone-100 text-stone-700 border-stone-200 dark:bg-stone-800 dark:text-stone-300 dark:border-stone-700', $actionName],
                            };
                        @endphp
                        <tr class="border-b border-stone-100 dark:border-stone-800/80 hover:bg-stone-50/50 dark:hover:bg-cyan-500/5 transition">
                            
                            {{-- Waktu --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-bold text-stone-800 dark:text-stone-200">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                                </p>
                                <p class="text-[10px] text-stone-400 dark:text-stone-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Aktor --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($log->actor)
                                    <p class="font-bold text-stone-800 dark:text-stone-200">{{ $log->actor->name }}</p>
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500 font-mono">{{ $log->actor->email }}</p>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-400">
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
                                <span class="text-stone-700 dark:text-stone-300 font-medium">
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
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 text-[11px] font-semibold transition border border-stone-200 dark:border-stone-700"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Diff (JSON)
                                    </button>
                                @else
                                    <span class="text-stone-400 dark:text-stone-500 italic text-[11px]">-</span>
                                @endif
                            </td>

                            {{-- IP & Device Metadata --}}
                            <td class="px-5 py-4 text-[11px]">
                                @if(!empty($log->metadata['ip_address']))
                                    <p class="font-mono text-stone-700 dark:text-stone-300 font-semibold">{{ $log->metadata['ip_address'] }}</p>
                                @endif
                                @if(!empty($log->metadata['user_agent']))
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500 line-clamp-1 max-w-[200px]" title="{{ $log->metadata['user_agent'] }}">
                                        {{ $log->metadata['user_agent'] }}
                                    </p>
                                @endif
                                @if(empty($log->metadata['ip_address']) && empty($log->metadata['user_agent']))
                                    <span class="text-stone-400 dark:text-stone-500 italic">-</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-stone-400 dark:text-stone-500">
                                <svg class="w-8 h-8 text-stone-300 dark:text-stone-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="px-6 py-4 border-t border-stone-100 dark:border-stone-800 bg-stone-50/50 dark:bg-[#0E1420]/50">
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
            class="max-w-2xl w-full mx-auto bg-white dark:bg-[#131B2A] rounded-2xl shadow-2xl border border-stone-200 dark:border-stone-800 overflow-hidden"
            @click.away="selectedLog = null"
        >
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-stone-900 dark:to-stone-800 px-6 py-4 border-b border-stone-200 dark:border-stone-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-stone-800 dark:text-stone-100">
                        Detail Perubahan Audit #<span x-text="selectedLog?.id"></span>
                    </h3>
                    <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5" x-text="selectedLog?.action + ' pada ' + selectedLog?.entity"></p>
                </div>
                <button type="button" @click="selectedLog = null" class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 font-bold p-1 text-lg">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <template x-if="selectedLog?.old">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Nilai Sebelum (Old Values)
                        </h4>
                        <pre class="p-3 bg-stone-950 border border-stone-800 text-rose-300 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.old, null, 2)"></pre>
                    </div>
                </template>

                <template x-if="selectedLog?.new">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Nilai Sesudah (New Values)
                        </h4>
                        <pre class="p-3 bg-stone-950 border border-stone-800 text-emerald-300 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.new, null, 2)"></pre>
                    </div>
                </template>

                <template x-if="selectedLog?.meta && Object.keys(selectedLog?.meta).length > 0">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400 mb-1.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Metadata &amp; Header
                        </h4>
                        <pre class="p-3 bg-stone-950 border border-stone-800 text-blue-200 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed" x-text="JSON.stringify(selectedLog?.meta, null, 2)"></pre>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-stone-50 dark:bg-[#0E1420] border-t border-stone-100 dark:border-stone-800 flex justify-end">
                <button
                    type="button"
                    @click="selectedLog = null"
                    class="px-4 py-2 rounded-xl bg-stone-700 hover:bg-stone-800 dark:bg-stone-800 dark:hover:bg-stone-700 text-white text-xs font-semibold transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@endsection
