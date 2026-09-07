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
                    {{ __('Data akun Anda tersinkronisasi otomatis dari sistem SiPintu. Anda dapat memperbarui nomor WhatsApp aktif secara mandiri.') }}
                @else
                    {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
                @endif
            </p>
        </div>

        @if ($isSiPintuManaged)
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 dark:bg-stone-800/80 text-stone-600 dark:text-stone-300 border border-stone-200 dark:border-stone-700">
                    <svg class="w-3.5 h-3.5 text-stone-500 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Mode Lihat Saja (Read-Only)
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-700/50">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Nomor WhatsApp Dapat Diubah
                </span>
            </div>
        @endif
    </header>

    @if ($isSiPintuManaged)
        {{-- Form Profil untuk Guru & Siswa (Hanya Nomor WhatsApp yang Editable) --}}
        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('patch')

            {{-- Nama Lengkap (Read-Only) --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-1 mb-2">
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                        {{ __('Nama Lengkap') }}
                    </label>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-700 dark:text-cyan-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md border border-blue-200/60 dark:border-cyan-500/20">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Data tersinkronisasi dari SiPintu Gateway
                    </span>
                </div>
                <input id="name" type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->name }}" readonly disabled />
            </div>

            {{-- Email (Read-Only) --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-1 mb-2">
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                        {{ __('Email') }}
                    </label>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-700 dark:text-cyan-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md border border-blue-200/60 dark:border-cyan-500/20">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Data tersinkronisasi dari SiPintu Gateway
                    </span>
                </div>
                <input id="email" type="email" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->email }}" readonly disabled />
            </div>

            {{-- Detail Siswa --}}
            @if ($user->siswaProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-stone-200/70 dark:border-stone-800">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                {{ __('NIS') }}
                            </label>
                            <span class="text-[10px] text-stone-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        </div>
                        <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->siswaProfile->nis }}" readonly disabled />
                    </div>

                    @if ($user->siswaProfile->nisn)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                    {{ __('NISN') }}
                                </label>
                                <span class="text-[10px] text-stone-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            </div>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->siswaProfile->nisn }}" readonly disabled />
                        </div>
                    @endif

                    @if ($user->siswaProfile->class_name)
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                    {{ __('Kelas / Rombel') }}
                                </label>
                                <span class="text-[10px] text-stone-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            </div>
                            <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium text-sm focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->siswaProfile->class_name }}" readonly disabled />
                        </div>
                    @endif
                </div>
            @endif

            {{-- Detail Guru --}}
            @if ($user->guruProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-stone-200/70 dark:border-stone-800">
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                {{ __('NIP') }}
                            </label>
                            <span class="text-[10px] text-stone-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        </div>
                        <input type="text" class="w-full px-4 py-3 bg-stone-100 dark:bg-[#0B0F17] border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-400 rounded-xl font-medium font-mono text-sm focus:outline-none select-all transition-colors cursor-not-allowed" value="{{ $user->guruProfile->nip }}" readonly disabled />
                    </div>
                </div>
            @endif

            {{-- Field Nomor Telepon WhatsApp (TERBUKA / EDITABLE) --}}
            <div class="pt-4 border-t border-stone-200/70 dark:border-stone-800">
                <div class="flex items-center justify-between mb-2">
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-stone-800 dark:text-stone-200">
                        {{ __('Nomor Telepon / WhatsApp Aktif') }} <span class="text-rose-600">*</span>
                    </label>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-500/20">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Dapat Diperbarui
                    </span>
                </div>

                <div class="relative w-full rounded-xl">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        inputmode="numeric"
                        required
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-[#0B0F17] text-stone-900 dark:text-stone-100 focus:ring-2 focus:ring-[#6F4E37] focus:border-transparent text-sm transition-colors"
                        value="{{ old('phone', $user->siswaProfile?->phone ?? $user->guruProfile?->phone) }}"
                        placeholder="Contoh: 081234567890"
                    />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                <p class="mt-1.5 text-xs text-stone-500 dark:text-stone-400">
                    Nomor WhatsApp ini digunakan untuk mengirimkan konfirmasi dan pengingat peminjaman barang TEFA.
                </p>
            </div>

            {{-- Tombol Simpan Perubahan Nomor --}}
            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="rounded-xl bg-[#6F4E37] hover:bg-[#5a3f2c] text-white dark:bg-gradient-to-r dark:from-amber-600 dark:to-[#6F4E37] dark:hover:from-amber-500 dark:hover:to-[#8B5A2B] dark:shadow-neon-amber font-medium px-5 py-2.5 transition-all text-sm shadow-sm active:scale-95 cursor-pointer inline-flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ __('Simpan Perubahan Nomor') }}</span>
                </button>

                @if (session('status') === 'profile-updated' || session('success'))
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-emerald-600 dark:text-neon-emerald font-medium flex items-center gap-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') ?? __('Tersimpan.') }}</span>
                    </p>
                @endif
            </div>
        </form>
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
