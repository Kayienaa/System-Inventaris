@extends('layouts.app')

@section('title', 'SiPintu API Gateway & Monitoring | TE-Vault')

@section('content')

<style>
    .sip-container {
        padding: 0.25rem 0 2rem;
    }

    .sip-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .sip-header-info h1 {
        font-size: 1.85rem;
        margin: 0 0 0.35rem;
        color: var(--brown-dark);
    }

    .sip-header-info p {
        margin: 0;
        color: var(--muted);
        font-size: 0.9rem;
    }

    /* ── Gateway Grid ── */
    .gateway-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .gw-card {
        background: var(--white);
        border: 1px solid var(--cream-dark);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .gw-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(111, 78, 55, 0.08);
    }

    .gw-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .gw-card-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gw-card-icon svg { width: 1.6rem; height: 1.6rem; }

    .gw-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.25rem 0.65rem;
        border-radius: 0.5rem;
    }

    .gw-badge.online { background: #d1fae5; color: #065f46; }
    .gw-badge.offline { background: #fee2e2; color: #991b1b; }
    .gw-badge.info { background: rgba(200, 155, 60, 0.15); color: #8B6914; }

    .gw-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--brown-dark);
        margin: 0 0 0.4rem;
    }

    .gw-card-desc {
        font-size: 0.85rem;
        color: var(--muted);
        margin: 0 0 1.25rem;
        line-height: 1.45;
    }

    .gw-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 0.5rem;
        line-height: 1;
    }

    .btn-gw-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.85rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        width: 100%;
        box-sizing: border-box;
    }

    .btn-gw-primary {
        background: var(--brown);
        color: #fff;
    }
    .btn-gw-primary:hover {
        background: var(--brown-dark);
        color: #fff;
    }

    .btn-gw-gold {
        background: var(--gold);
        color: #3B2610;
    }
    .btn-gw-gold:hover {
        background: #B3862A;
        color: #fff;
    }

    /* ── Diagnostics Panel ── */
    .diag-panel {
        background: var(--white);
        border: 1px solid var(--cream-dark);
        border-radius: 1.25rem;
        padding: 1.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .diag-panel h2 {
        font-size: 1.25rem;
        color: var(--brown-dark);
        margin: 0 0 0.5rem;
    }

    .diag-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        font-size: 0.85rem;
    }

    .diag-table tr {
        border-bottom: 1px solid var(--cream-dark);
    }
    .diag-table tr:last-child { border-bottom: none; }

    .diag-table td {
        padding: 0.85rem 0.5rem;
        vertical-align: middle;
    }

    .diag-table td:first-child {
        color: var(--muted);
        font-weight: 500;
        width: 240px;
    }

    .diag-table td:last-child {
        color: var(--text);
        font-weight: 600;
    }

    .code-pill {
        font-family: 'Courier New', monospace;
        background: var(--cream);
        border: 1px solid var(--cream-dark);
        padding: 0.2rem 0.5rem;
        border-radius: 0.4rem;
        font-size: 0.8rem;
    }
</style>

<div class="sip-container">

    {{-- Page Heading --}}
    <div class="sip-header">
        <div class="sip-header-info">
            <h1 class="brand-font">SiPintu Identity &amp; API Gateway</h1>
            <p>Pusat integrasi data terpadu SIJUNA (Siswa &amp; Guru) untuk TEVault Inventaris.</p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button onclick="pingGateway()" class="btn-gw-link" style="width: auto; background: var(--white); border: 1.5px solid var(--cream-dark); color: var(--brown);">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                </svg>
                Uji Ping Gateway
            </button>
        </div>
    </div>

    {{-- 3 Main Action Cards --}}
    <div class="gateway-cards-grid">

        {{-- Card 1: Data Pengguna / Siswa --}}
        <div class="gw-card">
            <div>
                <div class="gw-card-top">
                    <div class="gw-card-icon" style="background: rgba(200, 155, 60, 0.15); color: var(--gold);">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <span class="gw-badge info">SIJUNA Siswa</span>
                </div>
                <h3 class="gw-card-title">Data Pengguna (Siswa)</h3>
                <p class="gw-card-desc">Daftar siswa SMKN 1 Bangsri terdaftar di SIJUNA dengan NIS, akun, dan kontak.</p>
                <div class="gw-card-value">{{ number_format($summary['total_students'] ?? 2306) }} <span style="font-size: 1rem; color: var(--muted); font-weight: 500;">Siswa</span></div>
            </div>
            <a href="{{ route('sipintu.students.page') }}" class="btn-gw-link btn-gw-gold">
                Buka Data Pengguna &amp; Siswa →
            </a>
        </div>

        {{-- Card 2: Data Guru --}}
        <div class="gw-card">
            <div>
                <div class="gw-card-top">
                    <div class="gw-card-icon" style="background: rgba(111, 78, 55, 0.12); color: var(--brown);">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                        </svg>
                    </div>
                    <span class="gw-badge info">SIJUNA Guru</span>
                </div>
                <h3 class="gw-card-title">Data Dewan Guru</h3>
                <p class="gw-card-desc">Daftar guru &amp; tenaga pendidik SIJUNA dengan NIP, kode guru, WhatsApp, dan email.</p>
                <div class="gw-card-value">{{ number_format($summary['total_teachers'] ?? 71) }} <span style="font-size: 1rem; color: var(--muted); font-weight: 500;">Guru</span></div>
            </div>
            <a href="{{ route('sipintu.teachers.page') }}" class="btn-gw-link btn-gw-primary">
                Buka Data Guru SIJUNA →
            </a>
        </div>

        {{-- Card 3: Gateway Health --}}
        <div class="gw-card">
            <div>
                <div class="gw-card-top">
                    <div class="gw-card-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418"/>
                        </svg>
                    </div>
                    <span class="gw-badge {{ ($summary['is_connected'] ?? false) ? 'online' : 'offline' }}" id="badge-gw-status">
                        {{ ($summary['is_connected'] ?? false) ? '● Online' : '○ Offline' }}
                    </span>
                </div>
                <h3 class="gw-card-title">Koneksi Gateway</h3>
                <p class="gw-card-desc">Status koneksi server-to-server header authentication dengan SiPintu REST Gateway.</p>
                <div class="gw-card-value" style="font-size: 1.5rem; color: #059669;" id="val-gw-status">
                    {{ ($summary['is_connected'] ?? false) ? 'Terhubung' : 'Offline' }}
                </div>
            </div>
            <button onclick="pingGateway(true)" class="btn-gw-link" style="background: var(--cream-dark); color: var(--text);">
                Periksa Latensi &amp; Heartbeat
            </button>
        </div>

    </div>

    {{-- Connection Diagnostics Panel --}}
    <div class="diag-panel">
        <h2>Parameter &amp; Detail Koneksi</h2>
        <p style="margin: 0; color: var(--muted); font-size: 0.85rem;">Kredensial dan endpoint yang digunakan untuk komunikasi antar server.</p>

        <table class="diag-table">
            <tr>
                <td>Gateway API Endpoint</td>
                <td><span class="code-pill">{{ config('sipintu.api_url') }}</span></td>
            </tr>
            <tr>
                <td>Downstream Client ID</td>
                <td><span class="code-pill">{{ config('sipintu.client_id') }}</span></td>
            </tr>
            <tr>
                <td>Application Name</td>
                <td><strong>{{ $summary['client_name'] ?? 'TE-Vault' }}</strong></td>
            </tr>
            <tr>
                <td>Metode Autentikasi</td>
                <td>Server-to-Server Header Auth (<span class="code-pill">X-Client-ID</span> &amp; <span class="code-pill">X-Client-Secret</span>)</td>
            </tr>
            <tr>
                <td>Status Database Gateway</td>
                <td><span class="badge-status" style="color: #059669; font-weight: 700;">● Online</span> (Latensi DB: {{ $summary['latency_ms'] ?? 0.02 }} ms)</td>
            </tr>
            <tr>
                <td>Total Request Tercatat</td>
                <td id="diag-total-req">{{ $summary['total_requests'] ?? 0 }} requests</td>
            </tr>
            <tr>
                <td>Terakhir Terhubung</td>
                <td id="diag-last-conn">{{ $summary['last_connected_at'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

</div>

<script>
    function pingGateway(forceRefresh = false) {
        const badge = document.getElementById('badge-gw-status');
        const val = document.getElementById('val-gw-status');

        badge.className = 'gw-badge info';
        badge.textContent = 'Memeriksa...';

        fetch('{{ route("sipintu.status") }}' + (forceRefresh ? '?refresh=1' : ''))
            .then(r => r.json())
            .then(data => {
                if (data.connected) {
                    badge.className = 'gw-badge online';
                    badge.textContent = '● Online';
                    val.textContent = 'Terhubung';
                    val.style.color = '#059669';

                    if (data.data?.client_connection) {
                        const c = data.data.client_connection;
                        document.getElementById('diag-total-req').textContent = (c.total_api_requests || 0) + ' requests';
                        document.getElementById('diag-last-conn').textContent = c.last_connected_at || '-';
                    }
                } else {
                    badge.className = 'gw-badge offline';
                    badge.textContent = '○ Offline';
                    val.textContent = 'Tidak Terhubung';
                    val.style.color = '#dc2626';
                }
            })
            .catch(() => {
                badge.className = 'gw-badge offline';
                badge.textContent = '○ Error';
                val.textContent = 'Gagal';
                val.style.color = '#dc2626';
            });
    }
</script>

@endsection
