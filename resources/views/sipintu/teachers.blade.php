@extends('layouts.app')

@section('title', 'Data Guru SIJUNA | TE-Vault')

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
        margin-bottom: 1.5rem;
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

    .sip-stats-bar {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .sip-stat-pill {
        background: var(--white);
        border: 1px solid var(--cream-dark);
        border-radius: 0.85rem;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .sip-stat-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(111, 78, 55, 0.1);
        color: var(--brown);
    }

    .sip-stat-icon svg { width: 1.25rem; height: 1.25rem; }

    .sip-stat-text .label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--muted);
        font-weight: 600;
        margin: 0;
    }

    .sip-stat-text .val {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        line-height: 1.2;
    }

    /* ── Controls Bar ── */
    .sip-controls {
        background: var(--white);
        border: 1px solid var(--cream-dark);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .sip-search-box {
        position: relative;
        flex: 1;
        min-width: 260px;
    }

    .sip-search-box svg {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1.1rem;
        height: 1.1rem;
        color: var(--muted);
        pointer-events: none;
    }

    .sip-input {
        width: 100%;
        padding: 0.65rem 0.875rem 0.65rem 2.6rem;
        border: 1.5px solid var(--cream-dark);
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        background: var(--cream);
        color: var(--text);
        transition: all 0.2s;
    }

    .sip-input:focus {
        outline: none;
        background: var(--white);
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(200, 155, 60, 0.12);
    }

    .btn-sip-action {
        padding: 0.65rem 1.25rem;
        background: var(--brown);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }

    .btn-sip-action:hover {
        background: var(--brown-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-sip-outline {
        padding: 0.65rem 1rem;
        background: transparent;
        color: var(--brown);
        border: 1.5px solid var(--cream-dark);
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }

    .btn-sip-outline:hover {
        border-color: var(--brown);
        background: rgba(111, 78, 55, 0.05);
    }

    /* ── Data Table ── */
    .sip-card {
        background: var(--white);
        border-radius: 1rem;
        border: 1px solid var(--cream-dark);
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .sip-table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    .sip-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        text-align: left;
    }

    .sip-table thead {
        background: linear-gradient(180deg, #5A3C2A 0%, #6F4E37 100%);
        color: #fff;
    }

    .sip-table th {
        padding: 0.85rem 1rem;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .sip-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--cream-dark);
        vertical-align: middle;
        color: var(--text);
    }

    .sip-table tbody tr {
        transition: background 0.15s;
    }

    .sip-table tbody tr:hover {
        background: rgba(200, 155, 60, 0.04);
    }

    .badge-code {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.4rem;
        font-size: 0.75rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        background: rgba(111, 78, 55, 0.12);
        color: var(--brown-dark);
    }

    .badge-nip {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        font-family: 'Courier New', monospace;
        background: rgba(200, 155, 60, 0.12);
        color: #8B6914;
    }

    .badge-status-active {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.15rem 0.5rem;
        border-radius: 0.4rem;
        font-size: 0.72rem;
        font-weight: 600;
        background: #ecfdf5;
        color: #065f46;
    }
    .badge-status-active::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
    }

    .badge-gender {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 0.4rem;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-gender.l { background: #e0f2fe; color: #0369a1; }
    .badge-gender.p { background: #fce7f3; color: #be185d; }

    .wa-link {
        color: #059669;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 500;
    }
    .wa-link:hover { text-decoration: underline; }

    /* ── Empty & Loading States ── */
    .sip-state-box {
        padding: 4rem 1.5rem;
        text-align: center;
        color: var(--muted);
    }
    .sip-state-box svg {
        width: 3rem;
        height: 3rem;
        margin-bottom: 0.75rem;
        opacity: 0.4;
    }
    .sip-spinner {
        width: 2.25rem;
        height: 2.25rem;
        border: 3px solid var(--cream-dark);
        border-top-color: var(--gold);
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        margin: 0 auto 0.75rem;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="sip-container">

    {{-- Header --}}
    <div class="sip-header">
        <div class="sip-header-info">
            <h1 class="brand-font">Data Guru &amp; Tenaga Pendidik SIJUNA</h1>
            <p>Daftar seluruh dewan guru SMKN 1 Bangsri terintegrasi langsung via SiPintu Identity &amp; Gateway.</p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <form action="{{ route('admin.sync-sipintu') }}" method="POST" onsubmit="return confirm('Mulai sinkronisasi data GURU ke database lokal TE-VAULT? Proses ini memerlukan beberapa saat.')">
                @csrf
                <input type="hidden" name="type" value="teachers">
                <button type="submit" class="btn-sip-outline" style="background: var(--gold); color: #3B2610; border-color: var(--gold); font-weight: 700;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Sinkronkan ke DB Lokal
                </button>
            </form>
            <a href="{{ route('sipintu.students.page') }}" class="btn-sip-outline">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Lihat Data Pengguna/Siswa
            </a>
            <a href="{{ route('sipintu.index') }}" class="btn-sip-outline">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418"/>
                </svg>
                Status Gateway
            </a>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="sip-stats-bar">
        <div class="sip-stat-pill">
            <div class="sip-stat-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                </svg>
            </div>
            <div class="sip-stat-text">
                <p class="label">Total Guru SIJUNA</p>
                <p class="val" id="stat-total-teachers">{{ number_format($teachers['count'] ?? 71) }}</p>
            </div>
        </div>

        <div class="sip-stat-pill">
            <div class="sip-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="sip-stat-text">
                <p class="label">Status Koneksi Gateway</p>
                <p class="val" style="font-size: 1rem; color: #059669;">
                    {{ ($connection['connected'] ?? false) ? 'Terhubung (Online)' : 'Offline' }}
                </p>
            </div>
        </div>

        <div class="sip-stat-pill">
            <div class="sip-stat-icon" style="background: rgba(200, 155, 60, 0.12); color: var(--gold);">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                </svg>
            </div>
            <div class="sip-stat-text">
                <p class="label">Sumber Data</p>
                <p class="val" style="font-size: 0.95rem;">SIJUNA Service (Cached)</p>
            </div>
        </div>
    </div>

    {{-- Search and Filter Controls --}}
    <div class="sip-controls">
        <div class="sip-search-box">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text"
                   id="teacher-search-input"
                   class="sip-input"
                   placeholder="Cari berdasarkan Nama, NIP, Kode Guru (misal AW, BH), Email, atau Alamat..."
                   value="{{ $search }}">
        </div>

        <button type="button" class="btn-sip-action" onclick="performTeacherSearch()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            Cari Guru
        </button>

        <button type="button" class="btn-sip-outline" onclick="refreshTeacherData(true)" title="Sinkronkan data terbaru dari server SiPintu">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
            </svg>
            Sinkronkan Ulang
        </button>
    </div>

    {{-- Main Data Table Card --}}
    <div class="sip-card">
        <div class="sip-table-responsive">
            <div id="teacher-loading" class="sip-state-box" style="display: none;">
                <div class="sip-spinner"></div>
                <p>Memuat data guru dari SiPintu Gateway...</p>
            </div>

            <table class="sip-table" id="teachers-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode</th>
                        <th>NIP</th>
                        <th>Nama Guru &amp; Gelar</th>
                        <th>Panggilan</th>
                        <th>L/P</th>
                        <th>No HP / WhatsApp</th>
                        <th>Email SIJUNA</th>
                        <th>Status</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody id="teachers-tbody">
                    {{-- Populated by JavaScript --}}
                </tbody>
            </table>

            <div id="teacher-empty" class="sip-state-box" style="display: none;">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v.375c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
                <p id="teacher-empty-message">Tidak ada data guru yang cocok dengan kriteria pencarian.</p>
            </div>
        </div>
    </div>

</div>

<script>
    let allTeachers = @json($teachers['data'] ?? []);
    let filteredTeachers = [...allTeachers];

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('teacher-search-input');
        
        let timeout = null;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                performTeacherSearch();
            }, 200);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performTeacherSearch();
            }
        });

        if (allTeachers.length === 0) {
            refreshTeacherData();
        } else {
            renderTeacherTable();
        }
    });

    function performTeacherSearch() {
        const query = document.getElementById('teacher-search-input').value.trim().toLowerCase();
        
        if (!query) {
            filteredTeachers = [...allTeachers];
        } else {
            filteredTeachers = allTeachers.filter(t => {
                const nama = (t.nama || '').toLowerCase();
                const panggilan = (t.nama_panggilan || '').toLowerCase();
                const nip = String(t.nip || '');
                const kode = (t.kode || '').toLowerCase();
                const email = (t.user?.email || '').toLowerCase();
                const hp = String(t.hp || '');
                const alamat = (t.alamat || '').toLowerCase();

                return nama.includes(query) || panggilan.includes(query) || nip.includes(query) || kode.includes(query) || email.includes(query) || hp.includes(query) || alamat.includes(query);
            });
        }

        renderTeacherTable();
    }

    function renderTeacherTable() {
        const tbody = document.getElementById('teachers-tbody');
        const emptyBox = document.getElementById('teacher-empty');

        document.getElementById('stat-total-teachers').textContent = allTeachers.length.toLocaleString('id-ID');

        if (filteredTeachers.length === 0) {
            tbody.innerHTML = '';
            emptyBox.style.display = 'block';
            return;
        }

        emptyBox.style.display = 'none';

        let html = '';
        filteredTeachers.forEach((teacher, index) => {
            const rowNum = index + 1;
            const kode = teacher.kode || '-';
            const nip = teacher.nip || '-';
            const nama = teacher.nama || '-';
            const panggilan = teacher.nama_panggilan ? `"${teacher.nama_panggilan}"` : '-';
            const jk = (teacher.jk == 1) ? '<span class="badge-gender l">L</span>' : ((teacher.jk == 2) ? '<span class="badge-gender p">P</span>' : '-');
            const email = teacher.user?.email || '-';
            const hp = teacher.hp || '-';
            const alamat = teacher.alamat && teacher.alamat !== '-' ? teacher.alamat : '-';

            // WhatsApp link helper
            let hpFormatted = String(hp).replace(/[^0-9]/g, '');
            if (hpFormatted.startsWith('0')) hpFormatted = '62' + hpFormatted.substring(1);
            const waLink = hp && hp !== '-' ? `<a href="https://wa.me/${hpFormatted}" target="_blank" class="wa-link" title="Kirim Pesan WhatsApp">${escapeHtml(String(hp))}</a>` : '-';

            html += `
                <tr>
                    <td style="color: var(--muted); font-weight: 600;">${rowNum}</td>
                    <td><span class="badge-code">${escapeHtml(String(kode))}</span></td>
                    <td><span class="badge-nip">${escapeHtml(String(nip))}</span></td>
                    <td style="font-weight: 600; color: var(--brown-dark);">${escapeHtml(nama)}</td>
                    <td style="color: var(--muted); font-style: italic;">${escapeHtml(panggilan)}</td>
                    <td>${jk}</td>
                    <td>${waLink}</td>
                    <td style="color: #4b5563; font-size: 0.8rem;">${escapeHtml(email)}</td>
                    <td><span class="badge-status-active">Aktif</span></td>
                    <td style="color: var(--muted); max-width: 220px; font-size: 0.8rem;" class="truncate">${escapeHtml(alamat)}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function refreshTeacherData(forceRefresh = false) {
        const loadingBox = document.getElementById('teacher-loading');
        const emptyBox = document.getElementById('teacher-empty');
        const tbody = document.getElementById('teachers-tbody');

        loadingBox.style.display = 'block';
        emptyBox.style.display = 'none';
        tbody.innerHTML = '';

        const url = '{{ route("sipintu.teachers") }}' + (forceRefresh ? '?refresh=1' : '');

        fetch(url)
            .then(res => res.json())
            .then(res => {
                loadingBox.style.display = 'none';
                if (res.success && Array.isArray(res.data)) {
                    allTeachers = res.data;
                    performTeacherSearch();
                } else {
                    emptyBox.style.display = 'block';
                    document.getElementById('teacher-empty-message').textContent = res.message || 'Gagal memuat data guru.';
                }
            })
            .catch(err => {
                loadingBox.style.display = 'none';
                emptyBox.style.display = 'block';
                document.getElementById('teacher-empty-message').textContent = 'Kesalahan jaringan saat mengambil data dari gateway.';
            });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
</script>

@endsection
