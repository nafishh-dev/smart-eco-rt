<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fa">
    <title>Klaim &amp; Saldo Warga · Smart Eco-RT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="claim-page">
    <main class="claim-shell">
        <a class="brand claim-brand" href="{{ route('klaim') }}">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M19.5 4.5c-7.2.2-12.4 2.8-13.2 7.3-.5 2.8 1.4 5 4.1 5 4.8 0 8.6-5.8 9.1-12.3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.7-4.8 5-7.8 9.2-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <span class="brand-copy"><strong>Smart Eco-RT</strong><small>Portal warga RT 03</small></span>
        </a>

        <section class="claim-card content-card">
            <div class="claim-heading">
                <span class="claim-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 4.5 7v5c0 4.5 3.2 7.5 7.5 9 4.3-1.5 7.5-4.5 7.5-9V7L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <p class="eyebrow">SETORAN WARGA</p>
                <h1>Klaim Setoran</h1>
                <p>Masukkan kode dari perangkat untuk mencatat setoran dan mendapatkan poin minyak jelantah.</p>
            </div>

            <form class="claim-form" id="public-claim-form">
                <label class="field-label" for="claim-phone">Nomor telepon terdaftar</label>
                <input class="text-input" id="claim-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="Contoh: 081234567801" required>

                <label class="field-label" for="claim-otp">Kode OTP 4 digit</label>
                <input class="text-input otp-input" id="claim-otp" name="otp_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{4}" minlength="4" maxlength="4" placeholder="0000" required>

                <p class="claim-expiry">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Kode berlaku selama 3 menit setelah dibuat.
                </p>
                <button class="button button--primary button--full claim-submit" type="submit">Klaim Setoran</button>
                <p class="form-feedback" id="claim-feedback" role="status" hidden></p>
            </form>
        </section>

        <section class="balance-card content-card">
            <div class="balance-heading">
                <span class="balance-icon" aria-hidden="true">✦</span>
                <div><h2>Cek Saldo Poin</h2><p>Masukkan nomor telepon yang terdaftar.</p></div>
            </div>
            <form class="balance-form" id="balance-form">
                <label class="visually-hidden" for="balance-phone">Nomor telepon warga</label>
                <input class="text-input" id="balance-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="Nomor telepon" required>
                <button class="button button--secondary" type="submit">Cek Saldo</button>
            </form>
            <div class="balance-result" id="balance-result" role="status" hidden></div>
        </section>

        <a class="claim-admin-link" href="{{ route('login') }}">Masuk sebagai pengurus RT</a>
        <p class="auth-footer">Setoran organik dan anorganik tidak memperoleh poin.</p>
    </main>
    <section class="toast-region" id="toast-region" aria-live="polite" aria-atomic="true"></section>
</body>
</html>
