<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SITEFA') }}</title>

        {{-- Favicon Resmi TEFA SMKN 1 Bangsri --}}
        <link rel="icon" type="image/png" href="{{ asset('images/favicon-circle.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon-circle.png') }}">

        {{-- Anti-Flicker & System Scheme Sync Script --}}
        <script>
            (function() {
                const storedTheme = localStorage.getItem('color-theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Google Fonts: Plus Jakarta Sans & Poppins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFBF7] text-stone-800 dark:bg-[#0B0F17] dark:text-stone-100 transition-colors duration-300 antialiased font-sans">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 bg-[#FDFBF7] dark:bg-[#0B0F17] px-4 relative">
            <div class="absolute top-5 right-5 z-50">
                <button id="theme-toggle-guest" type="button" 
                        class="p-2.5 rounded-xl border border-stone-200 dark:border-stone-800 bg-white/80 dark:bg-stone-900/80 text-stone-600 dark:text-amber-400 shadow-sm backdrop-blur-md transition-all active:scale-95 cursor-pointer"
                        title="Ganti Mode Tampilan">
                    <!-- Icon Sun (Muncul saat Dark Mode) -->
                    <svg id="theme-toggle-light-icon-guest" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Icon Moon (Muncul saat Light Mode) -->
                    <svg id="theme-toggle-dark-icon-guest" class="hidden w-5 h-5 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-16 w-auto object-contain drop-shadow-sm">
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white dark:bg-[#131B2A] border border-stone-200 dark:border-stone-800 text-stone-900 dark:text-stone-100 rounded-2xl shadow-sm dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.5)] overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        <script>
            const themeToggleBtn = document.getElementById('theme-toggle-guest');
            const lightIcon = document.getElementById('theme-toggle-light-icon-guest');
            const darkIcon = document.getElementById('theme-toggle-dark-icon-guest');

            function syncIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    lightIcon?.classList.remove('hidden');
                    darkIcon?.classList.add('hidden');
                } else {
                    darkIcon?.classList.remove('hidden');
                    lightIcon?.classList.add('hidden');
                }
            }
            syncIcons();

            themeToggleBtn?.addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
                syncIcons();
                window.dispatchEvent(new CustomEvent('theme-changed'));
            });

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('color-theme')) {
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    syncIcons();
                    window.dispatchEvent(new CustomEvent('theme-changed'));
                }
            });
        </script>
    </body>
</html>
