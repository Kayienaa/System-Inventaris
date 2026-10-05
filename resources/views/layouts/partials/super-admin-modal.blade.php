@php
    $currentUser = Auth::user();
    $isPakAgung = $currentUser && ! $currentUser->hasRole('super_admin') && (
        $currentUser->email === 'agungmikro2@gmail.com'
        || $currentUser->guruProfile?->nip === '198103302010011016'
        || str_contains(strtolower($currentUser->name), 'agung')
    );
@endphp

@if($isPakAgung)
<div
    x-data="{
        open: {{ session('super_admin_switch_failed') ? 'true' : 'false' }},
        showPassword: false,
        init() {
            window.addEventListener('open-superadmin-modal', () => {
                this.open = true;
                this.$nextTick(() => {
                    this.$refs.superAdminPasswordInput?.focus();
                });
            });
        }
    }"
    @keydown.escape.window="open = false"
>
    <!-- Modal Backdrop & Dialog -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <!-- Overlay -->
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-stone-950/70 backdrop-blur-sm transition-opacity"
            @click="open = false"
        ></div>

        <!-- Modal Dialog -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#131B2A] border border-amber-300 dark:border-amber-500/30 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6"
                @click.stop
            >
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-stone-950 flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-stone-100 font-heading">
                            Masuk Sesi Super Admin
                        </h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400">
                            Pintasan khusus Pak Agung untuk switch ke <span class="font-mono text-amber-700 dark:text-amber-300 font-semibold">AdminInventaris@gmail.com</span>
                        </p>
                    </div>
                </div>

                @if(session('super_admin_switch_failed'))
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('error', 'Kata sandi Super Admin salah') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('switch-to-super-admin') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="super_admin_password" class="block text-xs font-semibold text-stone-700 dark:text-stone-300 mb-1.5">
                            Kata Sandi Super Administrator <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="super_admin_password"
                                name="password"
                                x-ref="superAdminPasswordInput"
                                required
                                placeholder="Masukkan kata sandi..."
                                class="w-full rounded-xl border border-stone-300 dark:border-stone-700 bg-stone-50 dark:bg-[#0B0F17] px-4 py-2.5 text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition pr-10"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 cursor-pointer"
                            >
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button
                            type="button"
                            @click="open = false"
                            class="px-4 py-2 rounded-xl border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 text-xs font-semibold transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-stone-950 text-xs font-bold shadow-md hover:shadow-lg active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                        >
                            <span>⚡ Konfirmasi & Masuk</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
