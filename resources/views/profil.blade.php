@extends('layouts.app')

@section('title', 'Profil Pengurus')

@section('content')
    <section class="profile-hero content-card">
        <span class="profile-avatar" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </span>
        <div class="profile-identity">
            <p class="eyebrow">AKUN PENGURUS</p>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <span class="role-badge">Admin &amp; Operator IoT</span>
        </div>
    </section>

    <section class="section-block">
        <div class="section-heading">
            <div><p class="eyebrow">INFORMASI AKUN</p><h2>Profil Pengurus RT</h2></div>
        </div>
        <div class="profile-details content-card">
            <div><span>Nama</span><strong>{{ $user->name }}</strong></div>
            <div><span>Email</span><strong>{{ $user->email }}</strong></div>
            <div><span>Jabatan</span><strong>Admin &amp; Operator IoT</strong></div>
            <div><span>Unit terhubung</span><strong>PANDES-HUB-01</strong></div>
        </div>
    </section>

    <section class="section-block">
        <div class="section-heading">
            <div><p class="eyebrow">KONTRIBUSI WARGA</p><h2>Minyak Jelantah Terkumpul</h2></div>
        </div>
        <div class="profile-stats">
            <article class="profile-stat profile-stat--amber">
                <span class="profile-stat__label">Total minyak jelantah</span>
                <strong>{{ number_format((float) $totalJelantah, 1, ',', '.') }} <small>Liter</small></strong>
                <span class="profile-stat__foot">Dari transaksi yang berhasil diklaim</span>
            </article>
            <article class="profile-stat profile-stat--green">
                <span class="profile-stat__label">Total Poin Eco</span>
                <strong>{{ number_format((int) $totalPoints) }} <small>Poin</small></strong>
                <span class="profile-stat__foot">Poin dari setoran minyak jelantah</span>
            </article>
        </div>
    </section>

    <form method="post" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button class="button button--logout button--full" type="submit">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Keluar (Logout)
        </button>
    </form>
@endsection
