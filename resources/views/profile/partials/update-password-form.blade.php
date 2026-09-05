<section>
    @php
        $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
    @endphp

    <header>
        <h2 class="text-xl font-bold text-stone-900 dark:text-white">
            {{ __('Keamanan & Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
            @if ($isSiPintuManaged)
                {{ __('Autentikasi akun Anda terhubung langsung dengan sistem Single Sign-On (SSO) SiPintu.') }}
            @else
                {{ __('Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.') }}
            @endif
        </p>
    </header>

    @if ($isSiPintuManaged)
        <div class="mt-6 bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-5 shadow-xs">
            <div class="flex items-start gap-3.5">
                <div class="p-2.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-neon-glowamber border border-amber-200/50 dark:border-amber-500/30 rounded-xl shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-800 dark:text-neon-glowamber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-stone-900 dark:text-stone-100">Penggantian Kata Sandi Dinonaktifkan</h4>
                    <p class="text-xs text-stone-600 dark:text-stone-300 mt-1 leading-relaxed">
                        Untuk menjaga integritas dan sinkronisasi Single Sign-On (SSO), perubahan kata sandi untuk akun Guru dan Siswa hanya dapat dilakukan melalui portal resmi SiPintu.
                    </p>
                    <div class="mt-3">
                        <a
                            href="{{ config('services.sipintu.portal_url', env('SIPINTU_PORTAL_URL', 'https://sipintu.smkn1bangsri.sch.id')) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-blue-600 hover:text-blue-800 dark:text-neon-cyan dark:hover:underline font-semibold inline-flex items-center gap-1 mt-2 text-xs"
                        >
                            <span>Ubah Kata Sandi di Portal SiPintu</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('put')

            <div>
                <label for="update_password_current_password" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                    {{ __('Kata Sandi Saat Ini') }}
                </label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition-colors" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                    {{ __('Kata Sandi Baru') }}
                </label>
                <input id="update_password_password" name="password" type="password" class="w-full px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition-colors" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                    {{ __('Konfirmasi Kata Sandi Baru') }}
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition-colors" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-medium px-5 py-2.5 transition-all text-sm shadow-sm active:scale-95 cursor-pointer">
                    {{ __('Simpan Kata Sandi') }}
                </button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-emerald-600 dark:text-neon-emerald font-medium"
                    >{{ __('Tersimpan.') }}</p>
                @endif
            </div>
        </form>
    @endif
</section>
