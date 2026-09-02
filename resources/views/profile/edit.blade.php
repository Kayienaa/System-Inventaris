<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm font-medium text-rose-800 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @php
                $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
            @endphp

            {{-- Banner SiPintu Gateway untuk Guru & Siswa --}}
            @if ($isSiPintuManaged)
                <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="p-3 bg-blue-600 text-white rounded-2xl shrink-0 shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-800 text-base">Profil Dikelola Terpusat via SiPintu</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $user->hasRole('guru') ? 'Guru' : 'Siswa' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1 max-w-2xl leading-relaxed">
                                Data akun Anda dikelola secara terpusat melalui SiPintu. Untuk memperbarui data pribadi atau mengganti kata sandi, silakan kunjungi portal resmi SiPintu.
                            </p>
                        </div>
                    </div>
                    <a
                        href="{{ config('services.sipintu.portal_url', env('SIPINTU_PORTAL_URL', 'https://sipintu.smkn1bangsri.sch.id')) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition shadow-sm shrink-0 active:scale-95"
                    >
                        <span>Kunjungi Portal SiPintu</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            @endif

            {{-- Informasi Profil --}}
            <div class="p-4 sm:p-8 bg-white shadow-sm border border-gray-200 sm:rounded-2xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-4 sm:p-8 bg-white shadow-sm border border-gray-200 sm:rounded-2xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account (hanya untuk Admin atau User Non-SiPintu) --}}
            @if (!$isSiPintuManaged)
                <div class="p-4 sm:p-8 bg-white shadow-sm border border-gray-200 sm:rounded-2xl">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
