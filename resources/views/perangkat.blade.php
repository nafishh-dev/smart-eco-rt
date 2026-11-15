@extends('layouts.app')

@section('title', 'Perangkat')

@section('content')
    @if ($device)
        <section class="device-hero content-card">
            <div class="device-hero__art" aria-hidden="true">
                <svg viewBox="0 0 120 120" fill="none"><rect x="22" y="18" width="76" height="75" rx="18" fill="currentColor" opacity=".1"/><path d="M40 43h40l-4 44H44l-4-44Zm-5-8h50m-32 0 3-9h18l3 9m-17 18v24m12-24v24" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M48 99h24" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
            </div>
            <div class="device-hero__body">
                <div class="device-hero__title">
                    <div>
                        <p class="eyebrow">SMART HUB TERHUBUNG</p>
                        <h2>{{ $device->name }}</h2>
                    </div>
                    <span class="online-badge {{ $isOnline ? 'online-badge--online' : 'online-badge--offline' }}" data-device-status-text>
                        <i></i>{{ $isOnline ? 'Online (Aktif)' : 'Offline' }}
                    </span>
                </div>
                <p class="device-id">{{ $device->hub_id }}</p>
                <p class="device-location">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                    {{ $device->location }}
                </p>
            </div>
        </section>
    @else
        <section class="content-card empty-state">
            <span aria-hidden="true">⌁</span>
            <strong>Perangkat belum terdaftar</strong>
            <p>Informasi perangkat akan tampil setelah hub ditambahkan.</p>
        </section>
    @endif

    <section class="section-block">
        <div class="section-heading">
            <div><p class="eyebrow">KENDALI JARAK JAUH</p><h2>Kontrol Utama</h2></div>
        </div>
        <div class="control-grid">
            <button class="control-card" type="button" data-open-modal="servo-modal">
                <span class="control-card__icon control-card__icon--green" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3v18m0-18 5 5m-5-5L7 8m5 13 5-5m-5 5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span><strong>Uji Buka Tutup</strong><small>Pilih sensor organik atau anorganik</small></span>
                <span class="control-card__arrow" aria-hidden="true">→</span>
            </button>
            <article class="control-card control-card--static">
                <span class="control-card__icon control-card__icon--amber" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 8v4l3 2m5-2a8 8 0 1 1-2.3-5.7M20 4v5h-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span><strong>Reset / Kalibrasi Lokal</strong><small>Tahan tombol fisik pada perangkat selama 3 detik.</small></span>
            </article>
        </div>
    </section>

    <section class="section-block">
        <div class="section-heading">
            <div><p class="eyebrow">PEMANTAUAN HUB</p><h2>Status Perangkat</h2></div>
        </div>
        <div class="device-status-grid">
            <article class="status-card">
                <span class="status-card__icon status-card__icon--green" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="7" width="17" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M22 10v4M7 10v4m4-4v4m4-4v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span><small>Baterai</small><strong>{{ $device?->battery_percent ?? 0 }}%</strong></span>
                <i class="status-card__dot"></i>
            </article>
            <article class="status-card">
                <span class="status-card__icon status-card__icon--blue" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M3 9a13 13 0 0 1 18 0M6.5 12.5a8 8 0 0 1 11 0M10 16a3 3 0 0 1 4 0m-2 4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span><small>Koneksi WiFi</small><strong class="status-card__truncate">{{ $device?->wifi_ssid ?? 'Belum terhubung' }}</strong></span>
                <i class="status-card__dot status-card__dot--blue"></i>
            </article>
            <article class="status-card">
                <span class="status-card__icon status-card__icon--amber" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 3 20h18L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 9v5m0 3h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span><small>Sensor</small><strong data-device-sensor>Siap</strong></span>
                <i class="status-card__dot"></i>
            </article>
        </div>
    </section>
@endsection
