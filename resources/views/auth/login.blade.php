<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{ tab: 'admin' }">
        {{-- Tab Switcher --}}
        <div class="flex mb-4 border-b border-slate-700">
            <button type="button" @click="tab = 'admin'"
                :class="tab === 'admin' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400'"
                class="px-4 py-2 border-b-2 text-sm font-medium">Admin</button>
            <button type="button" @click="tab = 'guru'"
                :class="tab === 'guru' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400'"
                class="px-4 py-2 border-b-2 text-sm font-medium">Guru</button>
            <button type="button" @click="tab = 'siswa'"
                :class="tab === 'siswa' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400'"
                class="px-4 py-2 border-b-2 text-sm font-medium">Siswa</button>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="login_as" :value="tab">

            {{-- ADMIN --}}
            <div x-show="tab === 'admin'" x-cloak>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
                <x-input-label for="password" class="mt-4" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="current-password" />
            </div>

            {{-- GURU --}}
            <div x-show="tab === 'guru'" x-cloak>
                <x-input-label for="nip" value="NIP" />
                <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip')" />
                <x-input-label for="password_guru" class="mt-4" :value="__('Password')" />
                <x-text-input id="password_guru" class="block mt-1 w-full" type="password" name="password" />
            </div>

            {{-- SISWA --}}
            <div x-show="tab === 'siswa'" x-cloak>
                <x-input-label for="nis" value="NIS" />
                <x-text-input id="nis" class="block mt-1 w-full" type="text" name="nis" :value="old('nis')" />
                <x-input-label for="tanggal_lahir" class="mt-4" value="Tanggal Lahir" />
                <x-text-input id="tanggal_lahir" class="block mt-1 w-full" type="date" name="tanggal_lahir" />
            </div>

            <x-input-error :messages="$errors->get('login')" class="mt-4" />

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>{{ __('Log in') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>