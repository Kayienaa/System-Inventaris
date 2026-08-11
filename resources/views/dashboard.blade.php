<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TEVault</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

    <style>
        body{
            font-family:Inter,sans-serif;
            background:#f8f6f2;
            margin:0;
        }

        .brand{
            font-family:'DM Serif Display',serif;
        }

        .sidebar{
            width:260px;
            background:#5b3a29;
            color:white;
            min-height:100vh;
            position:fixed;
            left:0;
            top:0;
        }

        .logo{
            text-align:center;
            padding:30px;
            border-bottom:1px solid rgba(255,255,255,.15);
        }

        .logo h1{
            margin:10px 0 0;
            font-size:34px;
            color:#d6ab67;
        }

        .logo p{
            margin:5px 0 0;
            color:#ddd;
            font-size:14px;
        }

        .menu a{
            display:block;
            padding:15px 30px;
            color:white;
            text-decoration:none;
            transition:.2s;
        }

        .menu a:hover{
            background:rgba(255,255,255,.08);
        }

        .content{
            margin-left:260px;
            padding:35px;
        }

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:35px;
        }

        .welcome{
            background:white;
            border-radius:18px;
            padding:30px;
            box-shadow:0 8px 25px rgba(0,0,0,.05);
            margin-bottom:30px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:20px;
        }

        .card{
            background:white;
            border-radius:18px;
            padding:25px;
            box-shadow:0 8px 20px rgba(0,0,0,.05);
        }

        .card small{
            color:#888;
        }

        .card h2{
            margin-top:10px;
            font-size:32px;
            color:#5b3a29;
        }
    </style>

</head>

<body>

<div class="sidebar">

    <div class="logo">
        <h1 class="brand">TE-Vault</h1>
        <p>Sistem Inventaris</p>
    </div>

    <div class="menu">

        <a href="#">🏠 Dashboard</a>

        <a href="{{ route('items.index') }}">📦 Barang</a>

        <a href="{{ route('categories.index') }}">🏷️ Kategori</a>

        <a href="{{ route('borrowings.mine') }}">📋 Peminjaman</a>

        <a href="{{ route('profile.edit') }}">👤 Profil</a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button style="width:100%;border:none;background:none;color:white;text-align:left;padding:15px 30px;font-size:15px;cursor:pointer;">
                🚪 Logout
            </button>
        </form>

    </div>

</div>

<div class="content">

    <div class="header">

        <div>

            <h1 class="brand" style="font-size:40px;color:#5b3a29;margin:0;">
                Dashboard
            </h1>

            <p style="color:#777;">
                Selamat datang kembali,
                <b>{{ auth()->user()->name }}</b>
            </p>

        </div>

    </div>

    <div class="welcome">

        <h2 class="brand" style="margin-top:0;color:#5b3a29;">
            Selamat Datang di TE-Vault 👋
        </h2>

        <p style="color:#666;">
            Sistem Inventaris Barang di TEFA SMK Negeri 1 Bangsri.
        </p>

    </div>

    <div class="cards">

        <div class="card">
            <small>Total Aset</small>
            <h2>{{ $total_aset }}</h2>
        </div>

        <div class="card">
            <small>Kategori</small>
            <h2>{{ count($per_kategori) }}</h2>
        </div>

        <div class="card">
            <small>Status Aset</small>
            <h2>{{ count($status_aset) }}</h2>
        </div>

        <div class="card">
            <small>Terlambat</small>
            <h2>{{ count($overdue) }}</h2>
        </div>

    </div>

</div>

</body>
</html>