<section>
    @php
        $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
    @endphp

    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">
                {{ __('Informasi Profil') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                @if ($isSiPintuManaged)
                    {{ __('Data akun Anda tersinkronisasi otomatis dari sistem SiPintu.') }}
                @else
                    {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
                @endif
            </p>
        </div>

        @if ($isSiPintuManaged)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Mode Lihat Saja (Read-Only)
            </span>
        @endif
    </header>

    @if ($isSiPintuManaged)
        {{-- View-Only Profil untuk Guru & Siswa --}}
        <div class="mt-6 space-y-4">
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed border-gray-300" :value="$user->name" readonly disabled />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed border-gray-300" :value="$user->email" readonly disabled />
            </div>

            {{-- Detail Siswa --}}
            @if ($user->siswaProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <x-input-label :value="__('NIS')" />
                        <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed font-mono text-sm border-gray-300" :value="$user->siswaProfile->nis" readonly disabled />
                    </div>
                    @if ($user->siswaProfile->nisn)
                        <div>
                            <x-input-label :value="__('NISN')" />
                            <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed font-mono text-sm border-gray-300" :value="$user->siswaProfile->nisn" readonly disabled />
                        </div>
                    @endif
                    @if ($user->siswaProfile->class_name)
                        <div>
                            <x-input-label :value="__('Kelas / Rombel')" />
                            <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed text-sm border-gray-300" :value="$user->siswaProfile->class_name" readonly disabled />
                        </div>
                    @endif
                    @if ($user->siswaProfile->phone)
                        <div>
                            <x-input-label :value="__('Nomor Telepon / WhatsApp')" />
                            <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed text-sm border-gray-300" :value="$user->siswaProfile->phone" readonly disabled />
                        </div>
                    @endif
                </div>
            @endif

            {{-- Detail Guru --}}
            @if ($user->guruProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <x-input-label :value="__('NIP')" />
                        <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed font-mono text-sm border-gray-300" :value="$user->guruProfile->nip" readonly disabled />
                    </div>
                    @if ($user->guruProfile->phone)
                        <div>
                            <x-input-label :value="__('Nomor Telepon / WhatsApp')" />
                            <x-text-input type="text" class="mt-1 block w-full bg-gray-50 text-gray-700 cursor-not-allowed text-sm border-gray-300" :value="$user->guruProfile->phone" readonly disabled />
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @else
        {{-- Editable Profil untuk Admin / Local Users --}}
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
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
