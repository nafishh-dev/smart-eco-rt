<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fa">
    <title>Masuk · Smart Eco-RT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <a class="auth-brand" href="{{ route('login') }}" aria-label="Smart Eco-RT">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M19.5 4.5c-7.2.2-12.4 2.8-13.2 7.3-.5 2.8 1.4 5 4.1 5 4.8 0 8.6-5.8 9.1-12.3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.7-4.8 5-7.8 9.2-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Smart Eco-RT</strong><small>Kelola sampah bersama</small></span>
            </a>

            <div class="auth-heading">
                <p class="eyebrow">PORTAL PENGURUS RT 03</p>
                <h1>Selamat datang</h1>
                <p>Masuk untuk mengelola Smart Eco-RT.</p>
            </div>

            @if ($errors->any())
                <div class="auth-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form class="auth-form" method="post" action="{{ route('login.store') }}">
                @csrf
                <label class="field-label" for="email">Email</label>
                <input class="text-input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@email.com" required autofocus>

                <label class="field-label" for="password">Password</label>
                <input class="text-input" id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required>

                <button class="button button--primary button--full auth-submit" type="submit">Masuk</button>
            </form>

            <a class="auth-claim-link" href="{{ route('klaim') }}">Warga? Klaim Setoran &amp; Cek Poin di sini →</a>

            <aside class="demo-account">
                <strong>Akun demo untuk pengujian</strong>
                <span>Email <code>admin@smartecort.id</code></span>
                <span>Password <code>password</code></span>
            </aside>
        </section>
        <p class="auth-footer">Smart Eco-RT · Lingkungan bersih, warga sehat.</p>
    </main>
</body>
</html>
