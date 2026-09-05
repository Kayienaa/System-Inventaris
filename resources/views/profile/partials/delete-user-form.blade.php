<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-stone-900 dark:text-white">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
            {{ __('Setelah akun dihapus, semua sumber daya dan data yang terkait akan dihapus secara permanen. Pastikan Anda telah mengunduh data penting sebelum melanjutkan.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-medium px-5 py-2.5 transition-all text-sm shadow-sm active:scale-95 cursor-pointer inline-flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        <span>{{ __('Hapus Akun') }}</span>
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-stone-900 dark:text-white">
                {{ __('Apakah Anda yakin ingin menghapus akun ini?') }}
            </h2>

            <p class="mt-2 text-sm text-stone-500 dark:text-stone-400">
                {{ __('Setelah akun Anda dihapus, semua data dan sumber dayanya akan dihapus secara permanen. Silakan masukkan kata sandi untuk mengonfirmasi penghapusan akun.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">{{ __('Kata Sandi') }}</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full sm:w-3/4 px-4 py-3 bg-white dark:bg-[#0B0F17] border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 rounded-xl font-medium focus:ring-2 focus:ring-rose-500 focus:border-transparent outline-none transition-colors"
                    placeholder="{{ __('Kata Sandi') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2.5 bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 border border-stone-300 dark:border-stone-700 rounded-xl font-medium text-sm transition-colors cursor-pointer"
                >
                    {{ __('Batal') }}
                </button>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-medium text-sm transition-all shadow-sm active:scale-95 cursor-pointer"
                >
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
