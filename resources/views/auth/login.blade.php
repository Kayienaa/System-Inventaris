<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Login Sistem Inventaris Barang SMK Negeri 1 Bangsri">
    <title>Login – Sistem Inventaris Barang | SMK Negeri 1 Bangsri</title>

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
                            50:  '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                        }
                    },
                    boxShadow: {
                        'card': '0 8px 48px 0 rgba(37,99,235,0.10), 0 2px 8px 0 rgba(0,0,0,0.06)',
                        'btn':  '0 4px 16px 0 rgba(37,99,235,0.30)',
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        [x-cloak] { display: none !important; }

        body {
            background: linear-gradient(145deg, #EFF6FF 0%, #DBEAFE 40%, #EFF6FF 70%, #F8FAFC 100%);
            min-height: 100vh;
        }

        /* Custom input focus ring */
        .inp {
            display: block;
            width: 100%;
            border: 1.5px solid #CBD5E1;
            border-radius: 14px;
            background: #F8FAFC;
            color: #0F172A;
            font-size: 0.9375rem;
            padding: 1rem 1rem 1rem 3rem;
            height: 56px;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .inp::placeholder { color: #94A3B8; }
        .inp:focus {
            border-color: #3B82F6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .inp-error {
            border-color: #EF4444 !important;
            background: #FFF5F5;
        }
        .inp-error:focus {
            box-shadow: 0 0 0 3.5px rgba(239,68,68,0.13);
        }
        .inp-pr { padding-right: 3rem; }

        /* Spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.7s linear infinite; }

        /* Card slide-up */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card-anim { animation: slideUp 0.5s cubic-bezier(.22,.68,0,1.15) both; }

        /* Logo pulse glow */
        @keyframes logoPulse {
            0%,100% { box-shadow: 0 8px 24px rgba(37,99,235,0.28); }
            50%      { box-shadow: 0 8px 36px rgba(37,99,235,0.48); }
        }
        .logo-glow { animation: logoPulse 2.8s ease-in-out infinite; }
    </style>
</head>

<body class="flex min-h-screen items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-500 selection:text-white"
      x-data="{
          showPass: false,
          loading: false,
          onSubmit() { this.loading = true; }
      }">

    <div class="w-full max-w-md card-anim">

        <!-- ╔══════════════════════════════════════╗ -->
        <!-- ║               CARD                   ║ -->
        <!-- ╚══════════════════════════════════════╝ -->
        <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow: 0 8px 48px rgba(37,99,235,0.12), 0 2px 12px rgba(0,0,0,0.06);">

            <!-- Top accent bar -->
            <div class="h-1.5 w-full" style="background: linear-gradient(90deg,#1D4ED8,#3B82F6,#6366F1);"></div>

            <div class="px-8 pt-9 pb-10">

                <!-- ── Brand ── -->
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white mb-4 logo-glow">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight leading-snug">
                        Sistem Inventaris Barang
                    </h1>
                    <p class="text-sm font-semibold text-blue-600 mt-0.5">SMK Negeri 1 Bangsri</p>
                </div>

                <!-- ── Heading ── -->
                <div class="mb-6">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang 👋</h2>
                    <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
                        Silakan masuk ke akun Anda untuk mengakses Sistem Inventaris Barang SMK Negeri 1 Bangsri.
                    </p>
                </div>

                <!-- ── Session Status ── -->
                @if (session('status'))
                <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-emerald-700">{{ session('status') }}</p>
                </div>
                @endif

                <!-- ── Error Alert ── -->
                @if ($errors->any())
                <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3.5 mb-5">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-0.5">Login gagal</p>
                        @foreach ($errors->all() as $err)
                            <p class="text-sm text-red-600">{{ $err }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- ══════════ FORM ══════════ -->
                <form method="POST" action="{{ route('login') }}" @submit="onSubmit()" novalidate>
                    @csrf

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                            </span>
                            <input id="email" name="email" type="email"
                                   autocomplete="off"
                                   placeholder="example@gmail.com"
                                   required
                                   class="inp {{ $errors->has('email') ? 'inp-error' : '' }}">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </span>
                            <input id="password" name="password"
                                   :type="showPass ? 'text' : 'password'"
                                   autocomplete="new-password"
                                   placeholder="Password"
                                   required
                                   class="inp inp-pr">
                            <!-- Eye toggle -->
                            <button type="button"
                                    @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-blue-600 transition-colors duration-150"
                                    :title="showPass ? 'Sembunyikan' : 'Tampilkan'">
                                <!-- Eye open -->
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <!-- Eye slash -->
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
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
                                   style="width:16px;height:16px;border-radius:4px;border:1.5px solid #CBD5E1;cursor:pointer;accent-color:#2563EB;">
                            <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="loading"
                            style="border-radius:12px; background:#2563EB; color:#fff; width:100%; padding:0.875rem 1.5rem; font-size:1rem; font-weight:700; display:flex; align-items:center; justify-content:center; gap:8px; border:none; cursor:pointer; box-shadow:0 4px 16px rgba(37,99,235,0.30); transition:background 0.15s,transform 0.1s,box-shadow 0.15s;"
                            onmouseover="if(!this.disabled){this.style.background='#1D4ED8';this.style.boxShadow='0 6px 20px rgba(37,99,235,0.38)';this.style.transform='translateY(-1px)';}"
                            onmouseout="this.style.background='#2563EB';this.style.boxShadow='0 4px 16px rgba(37,99,235,0.30)';this.style.transform='translateY(0)';"
                            onmousedown="this.style.transform='translateY(0)';">
                        <!-- Normal -->
                        <span x-show="!loading" style="display:flex;align-items:center;gap:8px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                            Masuk
                        </span>
                        <!-- Loading -->
                        <span x-show="loading" x-cloak style="display:flex;align-items:center;gap:8px;">
                            <svg class="spinner" width="20" height="20" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path style="opacity:.8" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Sedang masuk…
                        </span>
                    </button>

                </form>

            </div>
        </div>

        <!-- Footer -->
        <p style="text-align:center;margin-top:1.5rem;font-size:0.75rem;color:#64748B;">
            &copy; {{ date('Y') }} SMK Negeri 1 Bangsri &bull; All rights reserved.
        </p>

    </div><!-- /max-w-md -->

</body>
</html>