<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fa">
    <title>@yield('title') · Smart Eco-RT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body data-live-status-url="{{ url('/api/live-status') }}">
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Smart Eco-RT, Dashboard">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M19.5 4.5c-7.2.2-12.4 2.8-13.2 7.3-.5 2.8 1.4 5 4.1 5 4.8 0 8.6-5.8 9.1-12.3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.7-4.8 5-7.8 9.2-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <span class="brand-copy"><strong>Smart Eco-RT</strong><small>Kelola sampah bersama</small></span>
        </a>

        <nav class="desktop-nav" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" @class(['nav-link', 'is-active' => request()->routeIs('dashboard')])>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3.5 10 8.5-7 8.5 7v10.5h-6v-6h-5v6h-6V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                Dashboard
            </a>
            <a href="{{ route('riwayat') }}" @class(['nav-link', 'is-active' => request()->routeIs('riwayat', 'riwayat.pdf')])>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5.5h16M4 12h16M4 18.5h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M19 16v5m-2.5-2.5H21.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Riwayat
            </a>
            <a href="{{ route('notifikasi') }}" @class(['nav-link', 'is-active' => request()->routeIs('notifikasi')])>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Notifikasi
                <span class="unread-badge" data-unread-badge @if (($unreadCount ?? 0) < 1) hidden @endif>{{ $unreadCount ?? 0 }}</span>
            </a>
            <a href="{{ route('perangkat') }}" @class(['nav-link', 'is-active' => request()->routeIs('perangkat')])>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 22h8m-4-4v4M7 10h3m-3 4h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Perangkat
            </a>
        </nav>
    </header>

    <main class="page-shell">
        <div class="page-heading">
            <div>
                <p class="eyebrow">SMART ECO-RT · RT 03</p>
                <h1>@yield('title')</h1>
            </div>
            @yield('heading-action')
        </div>
        @yield('content')
    </main>

    <nav class="mobile-nav" aria-label="Navigasi utama">
        <a href="{{ route('dashboard') }}" @class(['mobile-nav-link', 'is-active' => request()->routeIs('dashboard')]) @if (request()->routeIs('dashboard')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3.5 10 8.5-7 8.5 7v10.5h-6v-6h-5v6h-6V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('riwayat') }}" @class(['mobile-nav-link', 'is-active' => request()->routeIs('riwayat', 'riwayat.pdf')]) @if (request()->routeIs('riwayat', 'riwayat.pdf')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5.5h16M4 12h16M4 18.5h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('notifikasi') }}" @class(['mobile-nav-link', 'is-active' => request()->routeIs('notifikasi')]) @if (request()->routeIs('notifikasi')) aria-current="page" @endif>
            <span class="mobile-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="unread-badge mobile-badge" data-unread-badge @if (($unreadCount ?? 0) < 1) hidden @endif>{{ $unreadCount ?? 0 }}</span>
            </span>
            <span>Notifikasi</span>
        </a>
        <a href="{{ route('perangkat') }}" @class(['mobile-nav-link', 'is-active' => request()->routeIs('perangkat')]) @if (request()->routeIs('perangkat')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 22h8m-4-4v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Perangkat</span>
        </a>
    </nav>

    <section class="toast-region" id="toast-region" aria-live="polite" aria-atomic="true"></section>

    <dialog class="action-dialog" id="otp-modal" aria-labelledby="otp-modal-title">
        <form class="dialog-card" id="otp-form">
            <button class="dialog-close icon-button" type="button" data-close-modal aria-label="Tutup">×</button>
            <span class="dialog-icon dialog-icon--green" aria-hidden="true">✓</span>
            <h2 id="otp-modal-title">Klaim Kode OTP</h2>
            <p class="muted">Masukkan kode dari perangkat untuk mencatat setoran Anda.</p>
            <label class="field-label" for="otp-code">Kode OTP</label>
            <input id="otp-code" name="otp_code" class="text-input otp-input" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{4}" minlength="4" maxlength="4" placeholder="0000" required>
            <label class="field-label" for="citizen-name">Nama warga <span class="muted">(opsional)</span></label>
            <input id="citizen-name" name="citizen_name" class="text-input" type="text" maxlength="100" placeholder="Warga RT">
            <button class="button button--primary button--full" type="submit">Konfirmasi Klaim</button>
        </form>
    </dialog>

    <dialog class="action-dialog" id="servo-modal" aria-labelledby="servo-modal-title">
        <div class="dialog-card">
            <button class="dialog-close icon-button" type="button" data-close-modal aria-label="Tutup">×</button>
            <span class="dialog-icon dialog-icon--blue" aria-hidden="true">↕</span>
            <h2 id="servo-modal-title">Buka Tutup Sensor</h2>
            <p class="muted">Pilih wadah yang ingin dibuka.</p>
            <div class="dialog-actions">
                <button class="button button--primary" type="button" data-servo-direction="ORGANIK">Organik</button>
                <button class="button button--secondary" type="button" data-servo-direction="ANORGANIK">Anorganik</button>
            </div>
        </div>
    </dialog>

    @stack('scripts')
</body>
</html>
