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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#F8F6F2]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 bg-[#F8F6F2] px-4">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-16 w-auto object-contain drop-shadow-sm">
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl shadow-amber-900/5 border border-gray-200/80 overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
