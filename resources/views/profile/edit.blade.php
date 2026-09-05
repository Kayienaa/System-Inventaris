<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 dark:text-stone-100 leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Page Heading --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-stone-800 dark:text-stone-100 tracking-tight">
                        Profil Pengguna
                    </h1>
                    <p class="text-stone-500 dark:text-stone-400 mt-1 text-sm">
                        Informasi identitas akun dan pengelolaan keamanan Single Sign-On (SSO) TE-Vault
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-neon-glowamber border border-amber-200 dark:border-amber-500/30 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-neon-glowamber"></span>
                        {{ $user->hasRole('admin') ? 'Administrator' : ($user->hasRole('guru') ? 'Dewan Guru' : 'Siswa TEFA') }}
                    </span>
                </div>
            </div>

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50/90 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-500/30 p-4 text-sm font-medium text-rose-800 dark:text-rose-300 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success') || session('status') === 'profile-updated')
                <div class="rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-500/30 p-4 text-sm font-medium text-emerald-800 dark:text-neon-emerald flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-neon-emerald shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') ?? __('Profil berhasil diperbarui.') }}</span>
                </div>
            @endif

            @php
                $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
            @endphp

            {{-- Banner SiPintu Gateway untuk Guru & Siswa --}}
            @if ($isSiPintuManaged)
                <div class="bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-500/30 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="p-3 bg-blue-600 dark:bg-cyan-600 text-white rounded-2xl shrink-0 shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-stone-900 dark:text-white text-base">Profil Dikelola Terpusat via SiPintu</h3>
                                <span class="bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-cyan-300 border border-transparent dark:border-cyan-500/30 font-medium px-2.5 py-0.5 rounded-full text-xs">
                                    {{ $user->hasRole('guru') ? 'Guru' : 'Siswa' }}
                                </span>
                            </div>
                            <p class="text-sm text-stone-700 dark:text-stone-300 mt-1 max-w-2xl leading-relaxed">
                                Data akun Anda dikelola secara terpusat melalui SiPintu. Untuk memperbarui data pribadi atau mengganti kata sandi, silakan kunjungi portal resmi SiPintu.
                            </p>
                        </div>
                    </div>
                    <a
                        href="{{ config('services.sipintu.portal_url', env('SIPINTU_PORTAL_URL', 'https://sipintu.smkn1bangsri.sch.id')) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-cyan-600 dark:hover:bg-cyan-500 dark:shadow-neon-cyan font-medium rounded-xl px-5 py-2.5 transition-all text-sm shrink-0 active:scale-95 inline-flex items-center gap-2"
                    >
                        <span>Kunjungi Portal SiPintu</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            @endif

            {{-- Informasi Profil --}}
            <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-6 md:p-8 transition-colors duration-300">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-6 md:p-8 transition-colors duration-300">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account (hanya untuk Admin atau User Non-SiPintu) --}}
            @if (!$isSiPintuManaged)
                <div class="bg-white dark:bg-[#131B2A] border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] p-6 md:p-8 transition-colors duration-300">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
