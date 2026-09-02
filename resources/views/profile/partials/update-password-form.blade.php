<section>
    @php
        $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
    @endphp

    <header>
        <h2 class="text-lg font-bold text-gray-900">
            {{ __('Keamanan & Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            @if ($isSiPintuManaged)
                {{ __('Autentikasi akun Anda terhubung langsung dengan sistem Single Sign-On (SSO) SiPintu.') }}
            @else
                {{ __('Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.') }}
            @endif
        </p>
    </header>

    @if ($isSiPintuManaged)
        <div class="mt-6 rounded-2xl bg-gray-50 border border-gray-200 p-5">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-amber-100 text-amber-800 rounded-xl shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800">Penggantian Kata Sandi Dinonaktifkan</h4>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                        Untuk menjaga integritas dan sinkronisasi Single Sign-On (SSO), perubahan kata sandi untuk akun Guru dan Siswa hanya dapat dilakukan melalui portal resmi SiPintu.
                    </p>
                    <div class="mt-3">
                        <a
                            href="{{ config('services.sipintu.portal_url', env('SIPINTU_PORTAL_URL', 'https://sipintu.smkn1bangsri.sch.id')) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline"
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
                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    @endif
</section>
