@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="sensor-banner {{ $isOnline ? 'sensor-banner--online' : 'sensor-banner--offline' }}" data-sensor-banner role="status">
        <span class="sensor-banner__icon" aria-hidden="true">
            @if ($isOnline)
                <svg viewBox="0 0 24 24" fill="none"><path d="m5 12.5 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @else
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 8v5m0 4h.01M10.3 3.9 2.5 17.4A1.8 1.8 0 0 0 4 20h16a1.8 1.8 0 0 0 1.5-2.6L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endif
        </span>
        <span>
            <strong data-sensor-message>{{ $isOnline ? 'Semua Sensor Berfungsi Normal' : 'Perangkat Offline' }}</strong>
            <small data-sensor-description>{{ $isOnline ? 'Data diperbarui langsung dari perangkat.' : 'Menunggu perangkat mengirim data terbaru.' }}</small>
        </span>
        <span class="status-dot {{ $isOnline ? 'status-dot--online' : 'status-dot--offline' }}" data-device-status aria-label="{{ $isOnline ? 'Online' : 'Offline' }}"></span>
    </section>

    <section class="summary-grid" aria-label="Ringkasan">
        <article class="summary-card summary-card--weight">
            <span class="summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M5 8h14l1.5 12h-17L5 8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8a3 3 0 0 1 6 0m-4-3h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="summary-label">Total Berat Sampah Terkumpul</p>
                <p class="summary-value"><span data-total-weight>{{ number_format($totalWeight, 1, '.', '') }}</span><span class="summary-unit">kg</span></p>
                <p class="summary-footnote">Kontribusi warga RT 03</p>
            </div>
        </article>
        <article class="summary-card summary-card--online">
            <span class="summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 9a13 13 0 0 1 18 0M6.5 12.5a8 8 0 0 1 11 0M10 16a3 3 0 0 1 4 0m-2 4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="summary-label">Status Perangkat</p>
                <p class="summary-value summary-value--small" data-device-status-text>{{ $isOnline ? 'Online' : 'Offline' }}</p>
                <p class="summary-footnote">{{ $device?->name ?? 'Perangkat belum terdaftar' }}</p>
            </div>
        </article>
    </section>

    <div class="section-heading">
        <div>
            <p class="eyebrow">KONDISI TERKINI</p>
            <h2>Monitoring Tempat Sampah</h2>
        </div>
        <span class="live-indicator"><i></i> Live</span>
    </div>

    <section class="bin-grid" aria-label="Kondisi tempat sampah">
        @foreach (['organik', 'anorganik', 'jelantah'] as $category)
            @php($bin = $bins->get($category))
            @if ($bin)
                <article class="bin-card bin-card--{{ $category }}" data-bin-card="{{ $category }}">
                    <div class="bin-card__top">
                        <span class="bin-icon" aria-hidden="true">
                            @if ($category === 'organik')
                                <svg viewBox="0 0 24 24" fill="none"><path d="M19.5 4.5c-7.2.2-12.4 2.8-13.2 7.3-.5 2.8 1.4 5 4.1 5 4.8 0 8.6-5.8 9.1-12.3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.7-4.8 5-7.8 9.2-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            @elseif ($category === 'anorganik')
                                <svg viewBox="0 0 24 24" fill="none"><path d="M8 3h8l1 3h3v2H4V6h3l1-3Zm-2 7h12l-1 11H7L6 10Zm3 2v6m6-6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none"><path d="M9 3h6m-5 0v5l-5.5 9.2A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.8L14 8V3m-6 10h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </span>
                        <span class="bin-status {{ $bin->status === 'hampir_penuh' ? 'bin-status--warning' : 'bin-status--safe' }}" data-bin-status>
                            {{ $bin->status === 'hampir_penuh' ? 'Hampir Penuh' : 'Kondisi Baik' }}
                        </span>
                    </div>
                    <h3>{{ $bin->name }}</h3>
                    <p class="bin-subtitle">{{ $bin->subtitle }}</p>
                    <div class="bin-measure">
                        <strong><span data-bin-weight>{{ number_format($bin->current_weight, 1, '.', '') }}</span></strong>
                        <span>{{ $bin->unit }}</span>
                    </div>
                    <div class="capacity-heading">
                        <span>Kapasitas terisi</span>
                        <strong><span data-bin-capacity>{{ $bin->capacity_percent }}</span>%</strong>
                    </div>
                    <div class="progress-track" role="progressbar" aria-label="Kapasitas {{ $bin->name }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $bin->capacity_percent }}">
                        <span class="progress-fill" data-bin-progress style="width: {{ min(100, max(0, $bin->capacity_percent)) }}%"></span>
                    </div>
                </article>
            @endif
        @endforeach
    </section>

    <section class="quick-actions-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">AKSES CEPAT</p>
                <h2>Aksi Cepat</h2>
            </div>
        </div>
        <div class="quick-action-grid">
            <button class="quick-action quick-action--primary" type="button" data-open-modal="otp-modal">
                <span class="quick-action__icon" aria-hidden="true">＋</span>
                <span><strong>Klaim Kode OTP</strong><small>Catat setoran dan kumpulkan poin</small></span>
                <span class="quick-action__arrow" aria-hidden="true">→</span>
            </button>
            <button class="quick-action" type="button" data-open-modal="servo-modal">
                <span class="quick-action__icon quick-action__icon--blue" aria-hidden="true">↕</span>
                <span><strong>Buka Tutup Sensor</strong><small>Pilih wadah yang akan dibuka</small></span>
                <span class="quick-action__arrow" aria-hidden="true">→</span>
            </button>
            <button class="quick-action" type="button" data-api-action="/api/device/call-officer">
                <span class="quick-action__icon quick-action__icon--amber" aria-hidden="true">♧</span>
                <span><strong>Panggil Petugas</strong><small>Minta bantuan petugas kebersihan</small></span>
                <span class="quick-action__arrow" aria-hidden="true">→</span>
            </button>
        </div>
    </section>
@endsection
