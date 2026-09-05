<section>
    @php
        $isSiPintuManaged = $user->hasAnyRole(['guru', 'siswa']);
    @endphp

    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-stone-900 dark:text-white">
                {{ __('Informasi Profil') }}
            </h2>

            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                @if ($isSiPintuManaged)
                    {{ __('Data akun Anda tersinkronisasi otomatis dari sistem SiPintu.') }}
                @else
                    {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
                @endif
            </p>
        </div>

        @if ($isSiPintuManaged)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 dark:bg-stone-800/80 text-stone-600 dark:text-stone-300 border border-stone-200 dark:border-stone-700 shrink-0">
                <svg class="w-3.5 h-3.5 text-stone-500 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                    {{ __('Nama Lengkap') }}
                </label>
                <input id="name" type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium focus:outline-none select-all transition-colors" value="{{ $user->name }}" readonly disabled />
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                    {{ __('Email') }}
                </label>
                <input id="email" type="email" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium focus:outline-none select-all transition-colors" value="{{ $user->email }}" readonly disabled />
            </div>

            {{-- Detail Siswa --}}
            @if ($user->siswaProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-stone-200/70 dark:border-stone-800">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                            {{ __('NIS') }}
                        </label>
                        <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors" value="{{ $user->siswaProfile->nis }}" readonly disabled />
                    </div>
                    @if ($user->siswaProfile->nisn)
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                                {{ __('NISN') }}
                            </label>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors" value="{{ $user->siswaProfile->nisn }}" readonly disabled />
                        </div>
                    @endif
                    @if ($user->siswaProfile->class_name)
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                                {{ __('Kelas / Rombel') }}
                            </label>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium text-sm focus:outline-none select-all transition-colors" value="{{ $user->siswaProfile->class_name }}" readonly disabled />
                        </div>
                    @endif
                    @if ($user->siswaProfile->phone)
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                                {{ __('Nomor Telepon / WhatsApp') }}
                            </label>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium text-sm focus:outline-none select-all transition-colors" value="{{ $user->siswaProfile->phone }}" readonly disabled />
                        </div>
                    @endif
                </div>
            @endif

            {{-- Detail Guru --}}
            @if ($user->guruProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-stone-200/70 dark:border-stone-800">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                            {{ __('NIP') }}
                        </label>
                        <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors" value="{{ $user->guruProfile->nip }}" readonly disabled />
                    </div>
                    @if ($user->guruProfile->phone)
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                                {{ __('Nomor Telepon / WhatsApp') }}
                            </label>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-800 dark:text-stone-200 rounded-xl font-medium text-sm focus:outline-none select-all transition-colors" value="{{ $user->guruProfile->phone }}" readonly disabled />
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
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                    {{ __('Nama Lengkap') }}
                </label>
                <input id="name" name="name" type="text" class="w-full px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-stone-700 dark:text-stone-300 mb-2">
                    {{ __('Email') }}
                </label>
                <input id="email" name="email" type="email" class="w-full px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-[#6F4E37] dark:focus:ring-neon-cyan focus:border-transparent outline-none transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-stone-800 dark:text-stone-200">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 rounded-md focus:outline-none">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-emerald-600 dark:text-neon-emerald">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-medium px-5 py-2.5 transition-all text-sm shadow-sm active:scale-95 cursor-pointer">
                    {{ __('Simpan Perubahan') }}
                </button>

                @if (session('status') === 'profile-updated')
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
