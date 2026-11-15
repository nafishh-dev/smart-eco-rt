@extends('layouts.app')

@section('title', 'Notifikasi')

@section('heading-action')
    <button class="button button--quiet mark-read-button" type="button" data-read-all>
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Tandai Sudah Dibaca</span>
    </button>
@endsection

@section('content')
    <p class="page-intro">Pusat Peringatan &amp; Pemberitahuan</p>

    <nav class="filter-pills filter-pills--tabs" aria-label="Filter notifikasi">
        <a class="filter-pill {{ $type === null ? 'is-active' : '' }}" href="{{ route('notifikasi') }}">Semua</a>
        <a class="filter-pill {{ $type === 'warning' ? 'is-active' : '' }}" href="{{ route('notifikasi', ['type' => 'warning']) }}">Peringatan</a>
        <a class="filter-pill {{ $type === 'info' ? 'is-active' : '' }}" href="{{ route('notifikasi', ['type' => 'info']) }}">Info</a>
    </nav>

    <section class="notification-list" aria-label="Daftar notifikasi">
        @forelse ($notifications as $notification)
            <article class="notification-card {{ $notification->type === 'warning' && ! $notification->is_resolved ? 'notification-card--warning' : '' }} {{ $notification->is_resolved ? 'is-resolved' : '' }}"
                data-notification-card
                data-notification-id="{{ $notification->id }}"
                data-resolved="{{ $notification->is_resolved ? 'true' : 'false' }}">
                <span class="notification-icon {{ $notification->type === 'warning' ? 'notification-icon--warning' : 'notification-icon--info' }}" aria-hidden="true">
                    @if ($notification->type === 'warning')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 8v5m0 4h.01M10.3 3.9 2.5 17.4A1.8 1.8 0 0 0 4 20h16a1.8 1.8 0 0 0 1.5-2.6L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    @endif
                </span>
                <div class="notification-card__body">
                    <div class="notification-card__meta">
                        <span class="notification-type">{{ $notification->type === 'warning' ? 'Peringatan' : 'Info' }}</span>
                        <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</time>
                    </div>
                    <h2>{{ $notification->title }}</h2>
                    <p>{{ $notification->message }}</p>
                    <div class="notification-card__footer">
                        <span class="notification-state" data-notification-state>
                            {{ $notification->is_resolved ? 'Selesai' : 'Aktif' }}
                        </span>
                        @if ($notification->type === 'warning' && ! $notification->is_resolved && $notification->bin_category)
                            <button class="button button--danger button--small" type="button" data-empty-bin="{{ $notification->bin_category }}">
                                Kosongkan
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="content-card empty-state">
                <span aria-hidden="true">✓</span>
                <strong>Semua aman</strong>
                <p>Belum ada pemberitahuan untuk ditampilkan.</p>
            </div>
        @endforelse
    </section>
@endsection
