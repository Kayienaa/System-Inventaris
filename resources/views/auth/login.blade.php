<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Login Sistem Inventaris Barang SMK Negeri 1 Bangsri">
    <title>Login – Sistem Inventaris Barang | SMK Negeri 1 Bangsri</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        heading: ['"Poppins"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#F7F1E8',
                            100: '#EFE3D2',
                            500: '#C69A4B',
                            600: '#A97832',
                            700: '#805827',
                            800: '#5A3A24',
                        }
                    },
                    boxShadow: {
                        'card': '0 8px 48px 0 rgba(112,72,45,0.10), 0 2px 8px 0 rgba(0,0,0,0.06)',
                        'btn':  '0 4px 16px 0 rgba(169,120,50,0.28)',
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
            radial-gradient(circle at 15% 20%, rgba(196, 145, 58, 0.12), transparent 30%),
            radial-gradient(circle at 85% 80%, rgba(112, 72, 45, 0.10), transparent 32%),
            linear-gradient(145deg, #F7F1E8 0%, #EFE3D2 48%, #F9F6F1 100%);
        min-height: 100vh;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    html.dark body {
        background:
            radial-gradient(circle at 15% 20%, rgba(245, 158, 11, 0.08), transparent 30%),
            radial-gradient(circle at 85% 80%, rgba(14, 20, 32, 0.8), transparent 32%),
            linear-gradient(145deg, #0B0F17 0%, #0E1420 48%, #0B0F17 100%);
        color: #F3F4F6;
    }

    .inp {
        display: block;
        width: 100%;
        border: 1.5px solid #D8C9B8;
        border-radius: 14px;
        background: #FCFAF7;
        color: #3F2A1E;
        font-size: 0.9375rem;
        padding: 1rem 1rem 1rem 3rem;
        height: 56px;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s, color 0.15s;
        outline: none;
    }

    .inp::placeholder {
        color: #A89787;
    }

    .inp:focus {
        border-color: #A97832;
        background: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(169, 120, 50, 0.14);
    }

    html.dark .inp {
        border-color: #2D3748;
        background: #0E1420;
        color: #F3F4F6;
    }

    html.dark .inp::placeholder {
        color: #64748B;
    }

    html.dark .inp:focus {
        border-color: #F59E0B;
        background: #131B2A;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18);
    }

    .inp-error {
        border-color: #EF4444 !important;
        background: #FFF5F5;
    }

    html.dark .inp-error {
        background: rgba(239, 68, 68, 0.1);
    }

    .inp-error:focus {
        box-shadow: 0 0 0 3.5px rgba(239, 68, 68, 0.13);
    }

    .inp-pr {
        padding-right: 3rem;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner {
        animation: spin 0.7s linear infinite;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(28px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-anim {
        animation: slideUp 0.5s cubic-bezier(.22,.68,0,1.15) both;
    }

    @keyframes logoPulse {
        0%, 100% {
            box-shadow: 0 8px 24px rgba(169, 120, 50, 0.20);
        }

        50% {
            box-shadow: 0 8px 34px rgba(169, 120, 50, 0.34);
        }
    }

    .logo-glow {
        animation: logoPulse 2.8s ease-in-out infinite;
    }
</style>
</head>

<body class="relative flex min-h-screen items-center justify-center p-4 sm:p-6 antialiased selection:bg-amber-700 selection:text-white"
      x-data="{
          showPass: false,
          loading: false,
          onSubmit() { this.loading = true; }
      }">

    {{-- Tombol Toggle Dark/Light Mode di Pojok Kanan Atas Halaman Login --}}
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

    <div class="w-full max-w-md card-anim">

        <!-- ╔══════════════════════════════════════╗ -->
        <!-- ║               CARD                   ║ -->
        <!-- ╚══════════════════════════════════════╝ -->
        <div class="bg-white dark:bg-[#131B2A] border border-stone-200 dark:border-stone-800 text-stone-900 dark:text-stone-100 rounded-2xl overflow-hidden shadow-2xl dark:shadow-[0_8px_48px_rgba(0,0,0,0.5)] transition-colors duration-200">
            <!-- Top accent bar -->
            <div class="h-1.5 w-full" style="background: linear-gradient(90deg,#70482D,#A97832,#C69A4B);"></div>

            <div class="px-8 pt-9 pb-10">

                <!-- ── Brand ── -->
                <div class="flex flex-col items-center text-center mb-8">
                    @if (file_exists(public_path('images/logo-tefa.png')))
                        <div class="mb-4">
                            <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-16 w-auto object-contain drop-shadow-md">
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white mb-4 logo-glow"
                             style="background: linear-gradient(145deg,#70482D,#A97832);">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                    <h1 class="text-base font-extrabold font-heading text-slate-900 dark:text-stone-100 tracking-tight leading-snug">
                        Sistem Inventaris Barang
                    </h1>
                    <p class="text-sm font-semibold mt-0.5 text-[#A97832] dark:text-amber-400">SMK Negeri 1 Bangsri</p>
                </div>

                <!-- ── Heading ── -->
                <div class="mb-6">
                    <h2 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-stone-100 tracking-tight">Selamat Datang </h2>
                    <p class="text-sm text-slate-500 dark:text-stone-400 mt-1.5 leading-relaxed">
                        Silakan masuk ke akun Anda untuk mengakses Sistem Inventaris Barang SMK Negeri 1 Bangsri.
                    </p>
                </div>

                <!-- ── Session Status ── -->
                @if (session('status'))
                <div class="flex items-center gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-4 py-3 mb-5">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('status') }}</p>
                </div>
                @endif

                <!-- ── Error Alert ── -->
                @if ($errors->any())
                <div class="flex items-start gap-3 rounded-xl bg-red-50 dark:bg-rose-950/40 border border-red-200 dark:border-rose-800 px-4 py-3.5 mb-5">
                    <svg class="w-5 h-5 text-red-500 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-rose-300 mb-0.5">Login gagal</p>
                        @foreach ($errors->all() as $err)
                            <p class="text-sm text-red-600 dark:text-rose-400">{{ $err }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- ══════════ FORM ══════════ -->
                <form method="POST" action="{{ route('login') }}" @submit="onSubmit()" novalidate>
                    @csrf

                    <!-- Email Siswa / NIP Guru -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-stone-300 mb-1.5">Email Siswa / NIP Guru</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-slate-400 dark:text-stone-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                            </span>
                            <input id="email" name="email" type="text"
                                   autocomplete="off"
                                   placeholder="isi email disini"
                                   value="{{ old('email') }}"
                                   required
                                   class="inp {{ $errors->has('email') ? 'inp-error' : '' }}">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-stone-300 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-slate-400 dark:text-stone-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </span>
                            <input id="password" name="password"
                                   :type="showPass ? 'text' : 'password'"
                                   autocomplete="new-password"
                                   placeholder="isi sandi disini"
                                   required
                                   class="inp inp-pr">
                            <!-- Eye toggle -->
                            <button type="button"
                                    @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 dark:text-stone-500 hover:text-amber-700 dark:hover:text-amber-400 transition-colors duration-150 cursor-pointer"
                                    :title="showPass ? 'Sembunyikan' : 'Tampilkan'">
                                <!-- Eye open (saat password terlihat/terbuka) -->
                                <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <!-- Eye slash (saat password tersensor/tersembunyi) -->
                                <svg x-show="!showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center mb-7">
                        <label class="flex items-center gap-2 cursor-pointer group select-none">
                            <input type="checkbox" name="remember" id="remember"
                                   style="width:16px;height:16px;border-radius:4px;border:1.5px solid #CBD5E1;cursor:pointer;accent-color:#A97832;">
                            <span class="text-sm text-slate-600 dark:text-stone-300 group-hover:text-slate-800 dark:group-hover:text-stone-100 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="loading"
                            class="w-full flex items-center justify-center gap-2.5 leading-none py-3 px-4 font-bold text-white rounded-xl shadow-md transition"
                            style="background:linear-gradient(135deg,#70482D,#A97832); border:none; cursor:pointer; box-shadow:0 4px 16px rgba(112,72,45,0.30);"
                            onmouseover="if(!this.disabled){this.style.background='linear-gradient(135deg,#5A3A24,#805827)';this.style.boxShadow='0 6px 20px rgba(112,72,45,0.38)';this.style.transform='translateY(-1px)';}"
                            onmouseout="this.style.background='linear-gradient(135deg,#70482D,#A97832)';this.style.boxShadow='0 4px 16px rgba(112,72,45,0.30)';this.style.transform='translateY(0)';">
                        <!-- Normal -->
                        <span x-show="!loading" class="flex items-center justify-center gap-2.5 leading-none">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                            <span>Masuk</span>
                        </span>
                        <!-- Loading -->
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-2.5 leading-none">
                            <svg class="spinner w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path style="opacity:.8" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span>Sedang masuk…</span>
                        </span>
                    </button>
                </form>

            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-6 text-xs text-stone-500 dark:text-stone-400">
            &copy; {{ date('Y') }} SMK Negeri 1 Bangsri &bull; All rights reserved.
        </p>

    </div><!-- /max-w-md -->

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
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('color-theme')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                syncIcons();
            }
        });
    </script>
</body>
</html>