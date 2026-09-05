{{--
    TEVault Sidebar Navigation
    Hanya mengubah tampilan navigasi. Route, controller, middleware, dan logika auth tidak diubah.
--}}

<div x-data="{ sidebarOpen: false }" class="flex">

    {{-- ══════════════════════════════════
         OVERLAY (mobile)
    ══════════════════════════════════ --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-20 bg-black/40 lg:hidden"
        x-cloak
    ></div>

    {{-- ══════════════════════════════════
         SIDEBAR
    ══════════════════════════════════ --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 bg-[#3D2817] dark:bg-[#0E1420] border-r border-[#5a3f2c]/40 dark:border-stone-800"
    >
        {{-- ── Brand / Logo ── --}}
        <div class="flex flex-col items-center py-7 px-5 border-b border-white/10 dark:border-stone-800">
            {{-- Logo Resmi TEFA SMKN 1 Bangsri --}}
            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-2.5 mx-auto max-w-[80px] flex items-center justify-center shadow-sm mb-2">
                @if (file_exists(public_path('images/logo-tefa.png')))
                    <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-10 w-auto object-contain">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5
                                 M10 11.25h4
                                 M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375
                                 c0-.621-.504-1.125-1.125-1.125H3.375
                                 c-.621 0-1.125.504-1.125 1.125v.375
                                 c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                @endif
            </div>

            {{-- Brand name --}}
            <span class="brand-font text-2xl tracking-wide text-[#F8F6F2] dark:text-stone-100">TE-Vault</span>
            <span class="text-xs font-medium mt-0.5 text-center leading-tight uppercase tracking-wider text-white/60 dark:text-stone-400">
                SISTEM INVENTARIS
            </span>
        </div>

        {{-- ── User Info & Theme Toggle ── --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 dark:border-stone-800">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 bg-[#C89B3C] text-[#3B2610]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-semibold truncate text-[#F8F6F2] dark:text-stone-100">{{ Auth::user()->name }}</p>
                    <p class="text-xs truncate font-medium text-white/60 dark:text-stone-400">{{ Auth::user()->hasRole('admin') ? 'Administrator' : 'Peminjam' }}</p>
                </div>
            </div>
            <button id="theme-toggle" type="button" 
                    class="p-2 rounded-xl text-stone-300 hover:text-white bg-white/10 hover:bg-white/20 dark:bg-stone-900/90 dark:text-neon-glowcyan dark:hover:text-neon-cyan dark:border dark:border-cyan-500/30 dark:shadow-neon-sm transition-all duration-200 cursor-pointer"
                    title="Ubah Mode Tampilan">
                <svg id="theme-toggle-light-icon" class="hidden w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>

        {{-- ── Navigation Menu ── --}}
        <nav class="flex-1 overflow-y-auto scroll-smooth [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-[#8B5A2B]/40 [&::-webkit-scrollbar-thumb]:rounded-full px-3 py-4 space-y-0.5">

            @php
                $active = 'bg-white/10 text-white font-semibold dark:bg-cyan-500/10 dark:text-neon-cyan dark:border-r-2 dark:border-neon-cyan';
                $inactive = 'text-white/70 hover:bg-white/10 hover:text-white font-medium dark:text-stone-400 dark:hover:text-stone-100 dark:hover:bg-white/5';
                $link = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-all duration-150';
            @endphp

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="{{ $link }} {{ request()->routeIs('dashboard') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z
                             M3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z
                             M13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z
                             M13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                </svg>
                Dashboard
            </a>

            {{-- Barang (Hanya untuk Siswa & Guru) --}}
            @hasanyrole(['siswa', 'guru'])
            <a href="{{ route('assets.index') }}"
               class="{{ $link }} {{ request()->routeIs('assets.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5
                             M10 11.25h4
                             M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375
                             c0-.621-.504-1.125-1.125-1.125H3.375
                             c-.621 0-1.125.504-1.125 1.125v.375
                             c0 .621.504 1.125 1.125 1.125z
                             M10 11.25h4"/>
                </svg>
                Barang
            </a>
            @endhasanyrole

            {{-- Kategori --}}
            <a href="{{ route('categories.index') }}"
               class="{{ $link }} {{ request()->routeIs('categories.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581
                             c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3z
                             M6 6h.008v.008H6V6z"/>
                </svg>
                Kategori
            </a>

            {{-- Peminjaman --}}
            @hasrole('admin')
            <a href="{{ route('admin.borrowings.index') }}"
               class="{{ $link }} {{ request()->routeIs('admin.borrowings.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
                Peminjaman
            </a>
            @else
            <a href="{{ route('borrowings.mine') }}"
               class="{{ $link }} {{ request()->routeIs('borrowings.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
                Peminjaman
            </a>
            @endhasrole

            {{-- ── Administrasi (Super Admin Only) ── --}}
            @hasrole('admin')
            <div class="pt-4 pb-1 px-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-white/40">
                    Administrasi
                </p>
            </div>

            {{-- Kelola Aset (admin only) --}}
            <a href="{{ route('admin.assets.index') }}"
               class="{{ $link }} {{ request()->routeIs('admin.assets.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                </svg>
                Kelola Aset
            </a>

            {{-- Data Pengguna / Siswa (admin only) --}}
            <a href="{{ route('sipintu.students.page') }}"
               class="{{ $link }} {{ request()->routeIs('sipintu.students*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952
                             4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07
                             M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109
                             a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25
                             a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Data Pengguna
            </a>

            {{-- Data Guru (admin only) --}}
            <a href="{{ route('sipintu.teachers.page') }}"
               class="{{ $link }} {{ request()->routeIs('sipintu.teachers*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                </svg>
                Data Guru
            </a>

            {{-- Gateway SiPintu (admin only) --}}
            <a href="{{ route('sipintu.index') }}"
               class="{{ $link }} {{ request()->routeIs('sipintu.index') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418"/>
                </svg>
                Gateway SiPintu
            </a>

            {{-- Laporan (admin only) --}}
            <a href="{{ route('dashboard.analytics') }}"
               class="{{ $link }} {{ request()->routeIs('dashboard.analytics') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75
                             C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z
                             M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25
                             c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z
                             M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75
                             c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Laporan
            </a>

            {{-- Audit Log (admin only) --}}
            <a href="{{ route('admin.audit-logs.index') }}"
               class="{{ $link }} {{ request()->routeIs('admin.audit-logs.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Audit Log
            </a>
            @endhasrole

        </nav>

        {{-- ── Bottom Actions ── --}}
        <div class="px-3 pb-5 pt-2 border-t space-y-0.5" style="border-color: rgba(255,255,255,0.10);">

            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}"
               class="{{ $link }} {{ request()->routeIs('profile.*') ? $active : $inactive }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975
                             m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75
                             a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Profil
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="{{ $link }} w-full text-left transition-all duration-150"
                        style="color: rgba(248,246,242,0.70);"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#F8F6F2';"
                        onmouseout="this.style.background='transparent'; this.style.color='rgba(248,246,242,0.70)';">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5
                                 A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15
                                 m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    Logout
                </button>
            </form>

        </div>
    </aside>

    {{-- ══════════════════════════════════
         TOPBAR MOBILE (hamburger)
    ══════════════════════════════════ --}}
    <div class="lg:hidden fixed top-0 left-0 right-0 z-10 flex items-center justify-between px-4 py-3 shadow-sm bg-[#3D2817] dark:bg-[#0E1420] border-b border-[#5a3f2c]/40 dark:border-stone-800">

        <button @click="sidebarOpen = !sidebarOpen"
                class="p-1.5 rounded-lg focus:outline-none transition text-[#F8F6F2]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        <span class="brand-font text-lg tracking-wide text-[#F8F6F2]">TE-Vault</span>

        <div class="flex items-center gap-2">
            <button id="theme-toggle-mobile" type="button" 
                    class="p-1.5 rounded-lg text-white/80 hover:text-white bg-white/10 hover:bg-white/20 dark:text-neon-glowcyan dark:hover:text-neon-cyan dark:border dark:border-cyan-500/30 transition-all duration-200 cursor-pointer"
                    title="Ubah Mode Tampilan">
                <svg id="theme-toggle-light-icon-mobile" class="hidden w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="theme-toggle-dark-icon-mobile" class="hidden w-4 h-4 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-[#C89B3C] text-[#3B2610]">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>
    </div>

</div>
