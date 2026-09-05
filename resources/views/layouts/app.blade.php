<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'TE-Vault')</title>

    {{-- Favicon Resmi TEFA SMKN 1 Bangsri --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tefa.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-tefa.png') }}">

    {{-- Anti-Flicker Script --}}
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        :root {
            --brown-dark: #4A3022;
            --brown: #6F4E37;
            --brown-light: #8B6A4F;
            --gold: #C89B3C;
            --gold-light: #E4C77B;
            --cream: #F8F6F2;
            --cream-dark: #EEE9E1;
            --text: #30251F;
            --muted: #8A817A;
            --white: #FFFFFF;
        }

        html.dark {
            --brown-dark: #FBBF24;
            --brown: #8B5A2B;
            --brown-light: #A07855;
            --gold: #F59E0B;
            --gold-light: #FBBF24;
            --cream: #0B0F17;
            --cream-dark: #131B2A;
            --text: #F3F4F6;
            --muted: #94A3B8;
            --white: #131B2A;
        }

        html.dark body {
            background-color: #0B0F17 !important;
            color: #F3F4F6 !important;
        }

        html.dark .sidebar {
            background: #0E1420 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 8px 0 30px rgba(0, 0, 0, 0.5);
        }

        html.dark .topbar {
            background: rgba(19, 27, 42, 0.92) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(12px);
        }

        html.dark .topbar-title {
            color: #94A3B8 !important;
        }

        html.dark .topbar-date {
            color: #22D3EE !important;
        }

        html.dark .mobile-topbar {
            background: #0E1420 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .page-heading h1 {
            color: #F8FAFC !important;
        }

        html.dark .page-heading p {
            color: #94A3B8 !important;
        }

        html.dark .menu-link.active {
            background: rgba(6, 182, 212, 0.12) !important;
            color: #22D3EE !important;
            box-shadow: inset 3px 0 0 #06B6D4 !important;
        }

        html.dark .menu-link:hover:not(.active) {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #FFFFFF !important;
        }

        html.dark .welcome-card {
            background: linear-gradient(120deg, #131B2A 0%, #1E293B 60%, #0F172A 100%) !important;
            border: 1px solid rgba(6, 182, 212, 0.25) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 15px -2px rgba(6, 182, 212, 0.15) !important;
        }

        html.dark .stat-card {
            background: rgba(19, 27, 42, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.5) !important;
            transition: all 0.3s ease;
        }

        html.dark .stat-card:hover {
            border-color: rgba(6, 182, 212, 0.5) !important;
            box-shadow: 0 0 8px 0 rgba(6, 182, 212, 0.35) !important;
        }

        html.dark .stat-icon {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #22D3EE !important;
        }

        html.dark .stat-label {
            color: #94A3B8 !important;
        }

        html.dark .stat-value {
            color: #F8FAFC !important;
        }

        html.dark .panel, html.dark .category-card, html.dark .overdue-card {
            background: rgba(19, 27, 42, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.5) !important;
        }

        html.dark .panel-title, html.dark .section-heading h2 {
            color: #F8FAFC !important;
        }

        html.dark .panel-subtitle, html.dark .section-heading span {
            color: #94A3B8 !important;
        }

        html.dark .category-name {
            color: #CBD5E1 !important;
        }

        html.dark .category-bar {
            background: #1E293B !important;
        }

        html.dark .category-count {
            color: #F59E0B !important;
        }

        html.dark .overdue-item {
            background: rgba(30, 41, 59, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        html.dark .overdue-name {
            color: #F8FAFC !important;
        }

        html.dark .overdue-item-name {
            color: #94A3B8 !important;
        }

        html.dark .overdue-meta {
            color: #64748B !important;
        }

        html.dark .overdue-badge {
            background: rgba(244, 63, 94, 0.15) !important;
            color: #FB7185 !important;
            border: 1px solid rgba(244, 63, 94, 0.3) !important;
        }

        /* Global dark mode styling for form inputs & tables */
        html.dark input[type="text"],
        html.dark input[type="search"],
        html.dark input[type="number"],
        html.dark input[type="email"],
        html.dark input[type="password"],
        html.dark input[type="datetime-local"],
        html.dark select,
        html.dark textarea {
            background-color: #0B0F17 !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748B !important;
        }

        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus {
            border-color: #06B6D4 !important;
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.25) !important;
        }

        html.dark table thead {
            background-color: #0E1420 !important;
        }

        html.dark table thead th {
            color: #94A3B8 !important;
            border-color: #1E293B !important;
        }

        html.dark table tbody td {
            border-color: #1E293B !important;
            color: #CBD5E1 !important;
        }

        html.dark table tbody tr:hover {
            background-color: rgba(6, 182, 212, 0.04) !important;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
        }

        .brand-font {
            font-family: 'DM Serif Display', serif;
        }

        .mobile-overlay {
            display: none;
        }

        /* =========================
           LAYOUT
        ========================= */

        .dashboard-wrapper {
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 260px;
            background: linear-gradient(
                180deg,
                #5A3C2A 0%,
                #6F4E37 55%,
                #543827 100%
            );
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 50;
            box-shadow: 8px 0 30px rgba(55, 35, 23, 0.12);
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .brand-area {
            padding: 30px 24px 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .brand-logo {
            width: 58px;
            height: 58px;
            margin: 0 auto 12px;
            border-radius: 17px;
            background: linear-gradient(
                145deg,
                #DDBB68,
                #B8892F
            );
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.18);
        }

        .brand-logo svg {
            width: 30px;
            height: 30px;
        }

        .brand-name {
            margin: 0;
            font-size: 30px;
            letter-spacing: 0.02em;
            color: #FFFDF8;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.58);
        }

        .admin-badge {
            display: inline-flex;
            margin-top: 14px;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(200,155,60,0.18);
            border: 1px solid rgba(228,199,123,0.25);
            color: #E9D293;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* =========================
           USER
        ========================= */

        .user-area {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gold);
            color: var(--brown-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-name {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #FFFDF8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            margin: 2px 0 0;
            font-size: 11px;
            color: rgba(255,255,255,0.48);
        }

        /* =========================
           MENU
        ========================= */

        .menu {
            flex: 1;
            padding: 18px 12px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .menu::-webkit-scrollbar {
            width: 6px;
        }

        .menu::-webkit-scrollbar-thumb {
            background: rgba(139, 90, 43, 0.4);
            border-radius: 9999px;
        }

        .menu-label {
            padding: 0 12px;
            margin: 8px 0 9px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 13px;
            margin-bottom: 4px;
            border-radius: 11px;
            color: rgba(255,255,255,0.68);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.18s ease;
        }

        .menu-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            transform: translateX(2px);
        }

        .menu-link.active {
            background: rgba(255,255,255,0.12);
            color: white;
            box-shadow: inset 3px 0 0 var(--gold);
        }

        .menu-icon {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
        }

        .sidebar-bottom {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.10);
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 13px;
            border: 0;
            border-radius: 11px;
            background: transparent;
            color: rgba(255,255,255,0.62);
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .logout-button:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            min-height: 82px;
            padding: 0 38px;
            background: rgba(255,255,255,0.88);
            border-bottom: 1px solid #E8E1D8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-title {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .topbar-date {
            font-size: 12px;
            color: #A29A93;
        }

        /* =========================
           PAGE
        ========================= */

        .page {
            padding: 36px 38px 50px;
        }

        .page-heading {
            margin-bottom: 28px;
        }

        .page-heading h1 {
            margin: 0;
            font-size: 40px;
            line-height: 1.1;
            color: var(--brown-dark);
        }

        .page-heading p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        /* =========================
           WELCOME
        ========================= */

        .welcome-card {
            position: relative;
            overflow: hidden;
            padding: 28px 30px;
            border-radius: 18px;
            background:
                linear-gradient(
                    120deg,
                    #6F4E37 0%,
                    #805D42 60%,
                    #6A4731 100%
                );
            color: white;
            box-shadow: 0 12px 30px rgba(78, 51, 35, 0.14);
            margin-bottom: 26px;
        }

        .welcome-card::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            right: -55px;
            top: -85px;
            border: 1px solid rgba(228,199,123,0.22);
            border-radius: 50%;
        }

        .welcome-card::before {
            content: "";
            position: absolute;
            width: 110px;
            height: 110px;
            right: 40px;
            bottom: -70px;
            border: 1px solid rgba(228,199,123,0.16);
            border-radius: 50%;
        }

        .welcome-small {
            margin: 0 0 5px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #E4C77B;
        }

        .welcome-title {
            margin: 0;
            font-size: 28px;
            color: #FFFDF8;
        }

        .welcome-description {
            max-width: 600px;
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.7;
            color: rgba(255,255,255,0.68);
        }

        /* =========================
           STATISTICS
        ========================= */

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 0 14px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 17px;
            color: var(--brown-dark);
        }

        .section-heading span {
            font-size: 11px;
            color: #A49B92;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 1px solid #EDE6DE;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(65,45,30,0.04);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .stat-icon {
            width: 39px;
            height: 39px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F4EBDD;
            color: var(--brown);
        }

        .stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .stat-label {
            margin: 15px 0 0;
            color: #928980;
            font-size: 11px;
            font-weight: 600;
        }

        .stat-value {
            margin: 4px 0 0;
            color: var(--brown-dark);
            font-size: 29px;
            font-weight: 700;
        }

        /* =========================
           LOWER CONTENT
        ========================= */

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
            gap: 20px;
        }

        .panel {
            background: white;
            border: 1px solid #EDE6DE;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 5px 20px rgba(65,45,30,0.04);
        }

        .panel-title {
            margin: 0;
            font-size: 15px;
            color: var(--brown-dark);
        }

        .panel-subtitle {
            margin: 4px 0 20px;
            font-size: 11px;
            color: #A29A93;
        }

        /* =========================
           CATEGORY
        ========================= */

        .category-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .category-row {
            display: grid;
            grid-template-columns: 120px 1fr 35px;
            align-items: center;
            gap: 12px;
        }

        .category-name {
            font-size: 12px;
            color: #655B54;
            font-weight: 500;
        }

        .category-bar {
            height: 7px;
            overflow: hidden;
            background: #EEE8E0;
            border-radius: 999px;
        }

        .category-bar-inner {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #8B6A4F, #C89B3C);
        }

        .category-count {
            text-align: right;
            font-size: 11px;
            color: var(--brown);
            font-weight: 700;
        }

        .empty-state {
            padding: 28px 10px;
            text-align: center;
            color: #A39A92;
            font-size: 12px;
        }

        /* =========================
           OVERDUE
        ========================= */

        .overdue-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .overdue-item {
            padding: 13px;
            border-radius: 12px;
            background: #FCF8F2;
            border: 1px solid #F0E6D8;
        }

        .overdue-name {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            color: var(--brown-dark);
        }

        .overdue-item-name {
            margin: 3px 0 7px;
            font-size: 11px;
            color: #80766E;
        }

        .overdue-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 10px;
            color: #A09891;
        }

        .overdue-badge {
            display: inline-flex;
            padding: 3px 7px;
            border-radius: 999px;
            background: #F5E4D7;
            color: #9A5C35;
            font-size: 9px;
            font-weight: 700;
        }

        /* =========================
           MOBILE
        ========================= */

        .mobile-topbar {
            display: none;
        }

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 800px) {
            .sidebar {
                left: 0;
                top: 0;
                bottom: 0;
                width: min(82vw, 290px);
                transform: translateX(-105%);
                transition: transform 0.25s ease;
                box-shadow: 12px 0 30px rgba(55, 35, 23, 0.24);
                z-index: 60;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .mobile-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(18, 12, 10, 0.45);
                z-index: 55;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
            }

            .mobile-overlay.mobile-visible {
                opacity: 1;
                pointer-events: auto;
            }

            .mobile-topbar {
                display: flex;
                min-height: 64px;
                padding: 0 18px;
                align-items: center;
                justify-content: space-between;
                background: var(--brown);
                color: white;
                position: sticky;
                top: 0;
                z-index: 30;
            }

            .mobile-brand-wrap {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .mobile-menu-toggle {
                width: 38px;
                height: 38px;
                border: 0;
                border-radius: 10px;
                background: rgba(255,255,255,0.08);
                color: white;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .mobile-menu-toggle svg {
                width: 20px;
                height: 20px;
            }

            .mobile-brand {
                font-size: 23px;
                color: #FFFDF8;
            }

            .mobile-user {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: var(--gold);
                color: var(--brown-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 800;
            }

            .topbar {
                display: none;
            }

            .page {
                padding: 25px 18px 40px;
            }

            .page-heading h1 {
                font-size: 32px;
            }

            .page-heading p,
            .section-heading span,
            .panel-subtitle,
            .welcome-description,
            .stat-label,
            .category-name,
            .overdue-item-name,
            .overdue-meta {
                line-height: 1.5;
            }
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .category-row {
                grid-template-columns: 95px 1fr 30px;
            }

            .welcome-card {
                padding: 23px;
            }

            .welcome-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body class="bg-[#FDFBF7] text-stone-800 dark:bg-[#0B0F17] dark:text-stone-100 transition-colors duration-300 antialiased font-sans">

<div class="dashboard-wrapper" x-data="{ sidebarOpen: false }">

    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="mobile-overlay"
        :class="sidebarOpen ? 'mobile-visible' : ''"
        x-cloak
    ></div>

    {{-- =========================
         SIDEBAR ADMIN
    ========================== --}}
    <aside class="sidebar" :class="sidebarOpen ? 'mobile-open' : ''">

        <div class="brand-area">

            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-2.5 mx-auto max-w-[80px] flex items-center justify-center shadow-sm mb-2">
                @if (file_exists(public_path('images/logo-tefa.png')))
                    <img src="{{ asset('images/logo-tefa.png') }}" alt="Logo TEFA SMKN 1 Bangsri" class="h-10 w-auto object-contain">
                @else
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                        style="width: 28px; height: 28px; color: white;"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0H3.75m16.5 0c.621 0 1.125-.504 1.125-1.125V6c0-.621-.504-1.125-1.125-1.125H3.75C3.129 4.875 2.625 5.379 2.625 6v.375c0 .621.504 1.125 1.125 1.125"
                        />
                    </svg>
                @endif
            </div>

            <h1 class="brand-font brand-name">TE-Vault</h1>

            <p class="brand-subtitle">
                SISTEM INVENTARIS
            </p>

            @if(auth()->user()?->hasRole('admin'))
                <span class="admin-badge">
                    Administrator
                </span>
            @else
                <span class="admin-badge" style="background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.85); border-color: rgba(255,255,255,0.2);">
                    Peminjam
                </span>
            @endif

        </div>

        <div class="user-area">

            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div style="min-width:0;">
                <p class="user-name">
                    {{ auth()->user()->name }}
                </p>

                <p class="user-role">
                    {{ auth()->user()?->hasRole('admin') ? 'Administrator' : 'Peminjam' }}
                </p>
            </div>

        </div>

        <nav class="menu overflow-y-auto scroll-smooth [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-[#8B5A2B]/40 [&::-webkit-scrollbar-thumb]:rounded-full">

            <div class="menu-label">
                Menu Utama
            </div>

            {{-- Dashboard --}}
            <a
                href="{{ route('dashboard') }}"
                class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z
                             M3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z
                             M13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z
                             M13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                </svg>

                Dashboard
            </a>

            {{-- Barang (Hanya untuk Siswa & Guru) --}}
            @hasanyrole(['siswa', 'guru'])
            <a
                href="{{ route('assets.index') }}"
                class="menu-link {{ request()->routeIs('assets.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0H3.75"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125V6c0-.621-.504-1.125-1.125-1.125H3.375C2.754 4.875 2.25 5.379 2.25 6v.375c0 .621.504 1.125 1.125 1.125z"/>
                </svg>

                Barang
            </a>
            @endhasanyrole

            {{-- Kategori --}}
            <a
                href="{{ route('categories.index') }}"
                class="menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                </svg>

                Kategori
            </a>

            {{-- Peminjaman --}}
            @hasrole('admin')
            <a
                href="{{ route('admin.borrowings.index') }}"
                class="menu-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>

                Peminjaman
            </a>
            @else
            <a
                href="{{ route('borrowings.mine') }}"
                class="menu-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>

                Peminjaman
            </a>
            @endhasrole

            @hasrole('admin')
            <div class="menu-label" style="margin-top:22px;">
                Administrasi
            </div>

            {{-- Kelola Aset (Admin Only) --}}
            <a
                href="{{ route('admin.assets.index') }}"
                class="menu-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                </svg>

                Kelola Aset
            </a>

            {{-- Data Pengguna --}}
            <a
                href="{{ route('sipintu.students.page') }}"
                class="menu-link {{ request()->routeIs('sipintu.students*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Data Pengguna
            </a>

            {{-- Data Guru --}}
            <a
                href="{{ route('sipintu.teachers.page') }}"
                class="menu-link {{ request()->routeIs('sipintu.teachers*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                </svg>
                Data Guru
            </a>

            {{-- Gateway SiPintu --}}
            <a
                href="{{ route('sipintu.index') }}"
                class="menu-link {{ request()->routeIs('sipintu.index') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418"/>
                </svg>
                Gateway SiPintu
            </a>

            {{-- Laporan --}}
            <a
                href="{{ route('dashboard.analytics') }}"
                class="menu-link {{ request()->routeIs('dashboard.analytics') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z
                             M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z
                             M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>

                Laporan
            </a>

            {{-- Audit Log (Admin Only) --}}
            <a
                href="{{ route('admin.audit-logs.index') }}"
                class="menu-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>

                Audit Log
            </a>
            @endhasrole

            {{-- Profil --}}
            <a
                href="{{ route('profile.edit') }}"
                class="menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
            >
                <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.5 20.25a7.5 7.5 0 0115 0"/>
                </svg>

                Profil
            </a>

        </nav>

        <div class="sidebar-bottom">

            <form
                action="{{ route('logout') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    <svg class="menu-icon" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M18 15l3-3m0 0l-3-3m3 3H9"/>
                    </svg>

                    Logout
                </button>

            </form>

        </div>

    </aside>


    {{-- =========================
         MOBILE TOPBAR
    ========================== --}}
    <div class="mobile-topbar">

        <div class="mobile-brand-wrap">
            <button
                type="button"
                class="mobile-menu-toggle"
                @click="sidebarOpen = !sidebarOpen"
                aria-label="Buka menu navigasi"
            >
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
                </svg>
            </button>

            <span class="brand-font mobile-brand">
                TE-Vault
            </span>
        </div>

        <div class="flex items-center gap-3">
            {{-- Tombol Toggle Dark/Light Mode Mobile --}}
            <button id="theme-toggle-mobile" type="button" 
                    class="p-2 rounded-xl text-white/80 hover:text-white bg-white/10 hover:bg-white/20 dark:text-neon-glowcyan dark:hover:text-neon-cyan dark:border dark:border-cyan-500/30 transition-all duration-200 cursor-pointer"
                    title="Ubah Mode Tampilan">
                <svg id="theme-toggle-light-icon-mobile" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="theme-toggle-dark-icon-mobile" class="hidden w-5 h-5 text-stone-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <div class="mobile-user">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>

    </div>


    {{-- =========================
         MAIN CONTENT
    ========================== --}}
    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="topbar-title">
                    TEFA · SMK Negeri 1 Bangsri
                </p>
            </div>

            <div class="flex items-center gap-4">
                {{-- Tombol Toggle Switch Dark/Light Mode --}}
                <button id="theme-toggle" type="button" 
                        class="p-2 rounded-xl text-stone-500 hover:text-stone-900 bg-stone-100/80 hover:bg-stone-200/80 dark:bg-stone-900/90 dark:text-neon-glowcyan dark:hover:text-neon-cyan dark:border dark:border-cyan-500/30 dark:shadow-neon-sm transition-all duration-200 cursor-pointer"
                        title="Ubah Mode Tampilan">
                    <!-- Ikon Matahari (Tampil saat Dark Mode aktif) -->
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Ikon Bulan (Tampil saat Light Mode aktif) -->
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <div class="topbar-date">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

        </header>

        <section class="page">

            @if (session('success'))
                <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 0.85rem; background: #ECFDF5; border: 1.5px solid #A7F3D0; color: #065F46; display: flex; align-items: flex-start; gap: 0.75rem; box-shadow: 0 2px 8px rgba(16,185,129,0.08);">
                    <svg style="width: 1.35rem; height: 1.35rem; flex-shrink: 0; color: #059669; margin-top: 0.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div style="font-size: 0.9rem; font-weight: 500; line-height: 1.5;">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 0.85rem; background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; display: flex; align-items: flex-start; gap: 0.75rem; box-shadow: 0 2px 8px rgba(239,68,68,0.08);">
                    <svg style="width: 1.35rem; height: 1.35rem; flex-shrink: 0; color: #DC2626; margin-top: 0.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div style="font-size: 0.9rem; font-weight: 500; line-height: 1.5;">{{ session('error') }}</div>
                </div>
            @endif

        @yield('content')

        {{ $slot ?? '' }}

        </section>


    </main>

</div>

{{-- Script Pengatur Dark Mode --}}
<script>
    function syncThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('[id^="theme-toggle-light-icon"]').forEach(el => {
            if (isDark) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
        document.querySelectorAll('[id^="theme-toggle-dark-icon"]').forEach(el => {
            if (isDark) {
                el.classList.add('hidden');
            } else {
                el.classList.remove('hidden');
            }
        });
    }
    syncThemeIcons();

    function toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
        syncThemeIcons();
        window.dispatchEvent(new CustomEvent('theme-changed'));
    }

    document.querySelectorAll('#theme-toggle, #theme-toggle-mobile, [id="theme-toggle"]').forEach(btn => {
        btn.addEventListener('click', toggleTheme);
    });
</script>

</body>
</html>