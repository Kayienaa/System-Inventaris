<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Lengkapi Nomor WhatsApp – SITEFA SMKN 1 Bangsri">
    <title>Lengkapi Nomor WhatsApp – SITEFA | SMK Negeri 1 Bangsri</title>

    {{-- Favicon Resmi TEFA SMKN 1 Bangsri --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-circle.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon-circle.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#F7F1E8',
                            100: '#EFE3D2',
                            200: '#DFCDAF',
                            500: '#C69A4B',
                            600: '#A97832',
                            700: '#805827',
                            800: '#5A3A24',
                            900: '#3D2517',
                        }
                    },
                    boxShadow: {
                        'card': '0 12px 48px 0 rgba(112,72,45,0.12), 0 2px 8px 0 rgba(0,0,0,0.06)',
                        'btn':  '0 6px 20px 0 rgba(169,120,50,0.32)',
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            background:
                radial-gradient(circle at 15% 20%, rgba(196, 145, 58, 0.15), transparent 30%),
                radial-gradient(circle at 85% 80%, rgba(112, 72, 45, 0.12), transparent 35%),
                linear-gradient(145deg, #F7F1E8 0%, #EFE3D2 48%, #F9F6F1 100%);
            min-height: 100vh;
        }

        .inp {
            display: block;
            width: 100%;
            border: 1.5px solid #D8C9B8;
            border-radius: 14px;
            background: #FCFAF7;
            color: #3F2A1E;
            font-size: 0.9375rem;
            padding: 1rem 1rem 1rem 3.25rem;
            height: 56px;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
        }

        .inp::placeholder {
            color: #A89787;
        }

        .inp:focus {
            border-color: #A97832;
            background: #FFFFFF;
            box-shadow: 0 0 0 3.5px rgba(169, 120, 50, 0.16);
        }

        .inp-error {
            border-color: #EF4444 !important;
            background: #FFF5F5;
        }

        .inp-error:focus {
            box-shadow: 0 0 0 3.5px rgba(239, 68, 68, 0.15);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-anim {
            animation: slideUp 0.5s cubic-bezier(.22,.68,0,1.15) both;
        }
    </style>
</head>
<body class="flex min-h-screen flex-col items-center justify-center p-4 sm:p-6 lg:p-8 selection:bg-brand-500 selection:text-white">

    <div class="w-full max-w-lg card-anim">
        {{-- Header Brand & Logo --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center gap-3 p-2 bg-white/70 backdrop-blur-md rounded-2xl border border-brand-100 shadow-xs mb-3">
                <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA" class="h-10 w-auto object-contain" onerror="this.style.display='none'">
                <div class="h-6 w-px bg-brand-200"></div>
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMKN 1 Bangsri" class="h-10 w-auto object-contain" onerror="this.style.display='none'">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                SITEFA
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-brand-700 tracking-wider uppercase mt-0.5">
                Sistem Inventaris TEFA SMKN 1 Bangsri
            </p>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white/95 backdrop-blur-lg border border-[#E6D8C8] rounded-3xl p-6 sm:p-8 shadow-card relative overflow-hidden">
            {{-- Top Decorative Gradient Bar --}}
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-amber-400"></div>

            {{-- User Identity Pill --}}
            <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-full bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-800 font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-stone-800 leading-tight">
                            {{ $user->name }}
                        </p>
                        <p class="text-xs text-stone-500 font-medium">
                            @if ($user->hasRole('guru'))
                                Guru @if($user->guruProfile?->nip)• NIP: {{ $user->guruProfile->nip }}@endif
                            @elseif ($user->hasRole('siswa'))
                                Siswa @if($user->siswaProfile?->class_name)• {{ $user->siswaProfile->class_name }}@endif
                            @else
                                {{ $user->email }}
                            @endif
                        </p>
                    </div>
                </div>

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                    Onboarding
                </span>
            </div>

            {{-- Information Notice Alert --}}
            <div class="mb-6 rounded-2xl bg-[#FDFBF7] border border-[#E8DCCB] p-4 flex items-start gap-3.5 shadow-xs">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/70 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    {{-- WhatsApp SVG Icon --}}
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-stone-800">
                        Pembaruan Kontak Verifikasi Peminjaman
                    </h2>
                    <p class="text-xs text-stone-600 mt-0.5 leading-relaxed">
                        Untuk keperluan konfirmasi peminjaman barang TEFA, silakan masukkan nomor WhatsApp aktif Anda terlebih dahulu.
                    </p>
                </div>
            </div>

            {{-- Form Submission --}}
            <form method="POST" action="{{ route('complete-phone.store') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-brand-800 mb-2">
                        Nomor WhatsApp Aktif <span class="text-rose-600">*</span>
                    </label>

                    <div class="relative w-full rounded-xl">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                            {{-- WhatsApp SVG Icon --}}
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>

                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            inputmode="numeric"
                            autocomplete="tel"
                            required
                            autofocus
                            placeholder="Contoh: 081234567890 atau 6281234567890"
                            value="{{ old('phone') }}"
                            class="inp pl-11 @error('phone') inp-error @enderror"
                        />
                    </div>

                    @error('phone')
                        <p class="mt-2 text-xs font-semibold text-rose-600 flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @else
                        <p class="mt-1.5 text-xs text-stone-500">
                            Pastikan nomor dapat menerima pesan WhatsApp notifikasi peminjaman TEFA. Format: <code class="font-mono text-stone-700 font-medium">08xxxxxxxxxx</code>.
                        </p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-3">
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full h-12 rounded-xl bg-gradient-to-r from-brand-700 via-brand-600 to-amber-700 hover:from-brand-800 hover:to-amber-800 text-white font-bold text-sm shadow-btn transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60"
                    >
                        <span x-show="!submitting" class="flex items-center gap-2">
                            <span>Simpan Nomor WhatsApp</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>

            {{-- Logout Option --}}
            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>Bukan akun Anda?</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-semibold text-brand-700 hover:text-brand-900 underline cursor-pointer">
                        Keluar / Ganti Akun
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer Note --}}
        <p class="text-center text-xs text-brand-700 mt-6 font-medium">
            &copy; {{ date('Y') }} SITEFA • Teaching Factory SMKN 1 Bangsri
        </p>
    </div>

</body>
</html>
