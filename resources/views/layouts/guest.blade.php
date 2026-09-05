<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TE-Vault') }}</title>

        {{-- Favicon Resmi TEFA SMKN 1 Bangsri --}}
        <link rel="icon" type="image/png" href="{{ asset('images/logo-tefa.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-tefa.png') }}">

        {{-- Anti-Flicker Script --}}
        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFBF7] text-stone-800 dark:bg-[#0B0F17] dark:text-stone-100 transition-colors duration-300 antialiased font-sans">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 bg-[#FDFBF7] dark:bg-[#0B0F17] px-4 relative">
            <div class="absolute top-4 right-4">
                <button id="theme-toggle" type="button" 
                        class="p-2 rounded-xl text-stone-500 hover:text-stone-900 bg-stone-100/80 hover:bg-stone-200/80 dark:bg-stone-900/90 dark:text-neon-glowcyan dark:hover:text-neon-cyan dark:border dark:border-cyan-500/30 dark:shadow-neon-sm transition-all duration-200"
                        title="Ubah Mode Tampilan">
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-16 w-auto object-contain drop-shadow-sm">
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white/95 dark:bg-[#131B2A]/90 backdrop-blur-md border border-stone-200/70 dark:border-stone-800/80 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        <script>
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            function syncThemeIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon?.classList.remove('hidden');
                    themeToggleDarkIcon?.classList.add('hidden');
                } else {
                    themeToggleDarkIcon?.classList.remove('hidden');
                    themeToggleLightIcon?.classList.add('hidden');
                }
            }
            syncThemeIcons();

            themeToggleBtn?.addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
                syncThemeIcons();
                window.dispatchEvent(new CustomEvent('theme-changed'));
            });
        </script>
    </body>
</html>
