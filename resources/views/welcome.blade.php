<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Inventaris Barang SMK Negeri 1 Bangsri.">
    <title>TE-Vault — Sistem Inventaris SMK Negeri 1 Bangsri</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .title-font {
            font-family: 'Playfair Display', serif;
        }

        .card {
            box-shadow:
                0 20px 60px rgba(79, 52, 35, 0.10),
                0 4px 16px rgba(79, 52, 35, 0.06);
        }

        .btn-masuk {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;

            background: #6F4A32;
            color: #FFFDF9;

            font-weight: 600;
            font-size: 0.95rem;

            padding: 0.8rem 2.4rem;
            border-radius: 0.65rem;

            text-decoration: none;

            box-shadow: 0 5px 16px rgba(111, 74, 50, 0.20);

            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .btn-masuk:hover {
            background: #4F3524;
            box-shadow: 0 8px 22px rgba(111, 74, 50, 0.28);
            transform: translateY(-2px);
        }

        .logo-box {
            width: 86px;
            height: 86px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 1.35rem;

            border-radius: 1.4rem;

            background: #F4EBDD;
            border: 1px solid #E5D2B8;

            color: #6F4A32;
        }

        .logo-box svg {
            width: 42px;
            height: 42px;
        }

        .decor-line {
            height: 1px;
            background: #DCC7AF;
            flex: 1;
        }
    </style>
</head>

<body style="
    min-height: 100vh;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;

    padding: 2rem 1.5rem;

    background:
        radial-gradient(circle at 10% 20%, rgba(200, 155, 60, 0.08), transparent 25%),
        radial-gradient(circle at 90% 80%, rgba(111, 74, 50, 0.08), transparent 30%),
        linear-gradient(145deg, #F5EFE7 0%, #FBF8F3 48%, #F2EAE0 100%);
">

    {{-- Dekorasi background --}}
    <div style="
        position: fixed;
        width: 260px;
        height: 260px;
        border: 1px solid rgba(111, 74, 50, 0.08);
        border-radius: 50%;
        top: -100px;
        left: -100px;
        pointer-events: none;
    "></div>

    <div style="
        position: fixed;
        width: 320px;
        height: 320px;
        border: 1px solid rgba(200, 155, 60, 0.10);
        border-radius: 50%;
        bottom: -160px;
        right: -120px;
        pointer-events: none;
    "></div>

    <div class="card" style="
        position: relative;
        z-index: 1;

        width: 100%;
        max-width: 650px;

        background: #FFFDF9;

        border: 1px solid #E8DCCF;
        border-radius: 1.25rem;

        padding: 3rem 3.5rem;

        text-align: center;
    ">

        {{-- Logo sementara --}}
        <div class="logo-box">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5
                    M10 11.25h4
                    M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375
                    c0-.621-.504-1.125-1.125-1.125H3.375
                    c-.621 0-1.125.504-1.125 1.125v.375
                    c0 .621.504 1.125 1.125 1.125z"
                />
            </svg>
        </div>

        {{-- Brand --}}
        <div style="margin-bottom: 1.5rem;">
            <p style="
                margin: 0 0 0.45rem;

                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.18em;

                color: #A27B36;
            ">
                TE-VAULT
            </p>

            <h1
                class="title-font"
                style="
                    margin: 0;

                    font-size: clamp(2rem, 5vw, 2.7rem);
                    line-height: 1.15;

                    color: #4F3524;
                "
            >
                Sistem Inventaris Barang
            </h1>

            <div style="
                display: flex;
                align-items: center;
                gap: 0.75rem;

                margin: 1rem auto 0;
                max-width: 360px;
            ">
                <div class="decor-line"></div>

                <p style="
                    margin: 0;

                    white-space: nowrap;

                    font-size: 0.78rem;
                    font-weight: 700;
                    letter-spacing: 0.14em;

                    color: #79563D;
                ">
                    SMK NEGERI 1 BANGSRI
                </p>

                <div class="decor-line"></div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <p style="
            max-width: 500px;
            margin: 0 auto 2rem;

            font-size: 0.93rem;
            line-height: 1.8;

            color: #78695E;
        ">
            Sistem terpusat untuk mengelola inventaris TEFA,
            mencatat peminjaman, memantau ketersediaan barang,
            dan membantu pengelolaan aset sekolah secara lebih
            tertata dan efisien.
        </p>

        {{-- Tombol --}}
        @auth
            <a href="{{ route('dashboard') }}" class="btn-masuk">
                <svg
                    width="19"
                    height="19"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"
                    />
                </svg>

                Buka Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-masuk">
                <svg
                    width="19"
                    height="19"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"
                    />
                </svg>

                Masuk ke Sistem
            </a>
        @endauth

        {{-- Footer --}}
        <div style="
            margin-top: 2.25rem;
            padding-top: 1.25rem;

            border-top: 1px solid #E9DED3;
        ">
            <p style="
                margin: 0;

                font-size: 0.72rem;
                letter-spacing: 0.02em;

                color: #A49589;
            ">
                &copy; {{ date('Y') }} SMK Negeri 1 Bangsri
                &bull;
                TE-Vault
            </p>
        </div>

    </div>

</body>
</html>