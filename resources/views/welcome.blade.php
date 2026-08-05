<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Inventaris Barang SMK Negeri 1 Bangsri.">
    <title>Sistem Inventaris Barang — SMK Negeri 1 Bangsri</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        .card {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .btn-masuk {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.65rem 2.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-masuk:hover {
            background-color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>

<body style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dbeafe 0%, #f0f6ff 50%, #ffffff 100%);
    padding: 1.5rem;
    margin: 0;
">

    <div class="card" style="
        background: #ffffff;
        border-radius: 0.75rem;
        width: 100%;
        max-width: 600px;
        padding: 2.5rem 3rem;
        text-align: center;
    ">

        {{-- Ikon Inventaris --}}
        <div style="margin-bottom: 1.25rem;">
            <svg style="width:48px; height:48px; color:#2563eb; display:inline-block;"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="#2563eb">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5
                         M10 11.25h4
                         M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375
                         c0-.621-.504-1.125-1.125-1.125H3.375
                         c-.621 0-1.125.504-1.125 1.125v.375
                         c0 .621.504 1.125 1.125 1.125z" />
            </svg>
        </div>

        {{-- Judul --}}
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem;">
            Sistem Inventaris Barang
        </h1>

        {{-- Subjudul --}}
        <p style="font-size: 1.05rem; font-weight: 600; color: #2563eb; margin: 0 0 1.25rem;">
            SMK Negeri 1 Bangsri
        </p>

        {{-- Garis pemisah --}}
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin-bottom: 1.25rem;">

        {{-- Deskripsi --}}
        <p style="font-size: 0.9rem; color: #64748b; line-height: 1.65; margin: 0 0 1.75rem;">
            Sistem ini digunakan untuk mengelola data barang inventaris sekolah, mencatat peminjaman,
            dan memantau ketersediaan aset secara terpusat dan efisien.
        </p>

        {{-- Tombol --}}
        @auth
            <a href="{{ route('dashboard') }}" class="btn-masuk">Buka Dashboard</a>
        @else
            <a href="{{ route('login') }}" id="btn-masuk" class="btn-masuk">Masuk</a>
        @endauth

        {{-- Footer --}}
        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 2rem; margin-bottom: 0;">
            &copy; {{ date('Y') }} SMK Negeri 1 Bangsri. All rights reserved.
        </p>

    </div>

</body>
</html>