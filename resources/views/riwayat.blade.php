@extends('layouts.app')

@section('title', 'Riwayat Setoran')

@section('content')
    <section class="weekly-banner">
        <span class="weekly-banner__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </span>
        <span><small>Frekuensi Setoran (7 hari terakhir)</small><strong>{{ $weeklyCount }} Setoran</strong></span>
        <span class="weekly-banner__note">Terima kasih sudah menjaga lingkungan 🌱</span>
    </section>

    <nav class="filter-pills" aria-label="Filter kategori setoran">
        <a class="filter-pill {{ $category === null ? 'is-active' : '' }}" href="{{ route('riwayat') }}">Semua</a>
        <a class="filter-pill {{ $category === 'organik' ? 'is-active' : '' }}" href="{{ route('riwayat', ['category' => 'organik']) }}">Organik</a>
        <a class="filter-pill {{ $category === 'anorganik' ? 'is-active' : '' }}" href="{{ route('riwayat', ['category' => 'anorganik']) }}">Anorganik</a>
        <a class="filter-pill {{ $category === 'jelantah' ? 'is-active' : '' }}" href="{{ route('riwayat', ['category' => 'jelantah']) }}">Minyak Jelantah</a>
    </nav>

    <section class="content-card history-card">
        <div class="list-heading">
            <div><p class="eyebrow">AKTIVITAS WARGA</p><h2>Setoran Terbaru</h2></div>
            <span class="count-chip">{{ $transactions->count() }} transaksi</span>
        </div>
        <div class="history-list">
            @forelse ($transactions as $transaction)
                @php($categoryClass = $transaction->category === 'anorganik' ? 'blue' : ($transaction->category === 'jelantah' ? 'amber' : 'green'))
                <article class="history-item">
                    <span class="category-icon category-icon--{{ $categoryClass }}" aria-hidden="true">
                        @if ($transaction->category === 'jelantah')
                            <svg viewBox="0 0 24 24" fill="none"><path d="M9 3h6m-5 0v5l-5.5 9.2A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.8L14 8V3m-6 10h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($transaction->category === 'anorganik')
                            <svg viewBox="0 0 24 24" fill="none"><path d="M8 3h8l1 3h3v2H4V6h3l1-3Zm-2 7h12l-1 11H7L6 10Zm3 2v6m6-6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none"><path d="M19.5 4.5c-7.2.2-12.4 2.8-13.2 7.3-.5 2.8 1.4 5 4.1 5 4.8 0 8.6-5.8 9.1-12.3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.5 20c1.7-4.8 5-7.8 9.2-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @endif
                    </span>
                    <div class="history-item__body">
                        <strong>{{ $transaction->description }}</strong>
                        <span>{{ $transaction->citizen_name }} · {{ $transaction->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <div class="history-item__amount">
                        <strong>{{ number_format($transaction->amount, 1, ',', '.') }} {{ $transaction->unit }}</strong>
                        @if ($transaction->category === 'jelantah' && $transaction->points_earned > 0)
                            <span class="points-badge">+{{ $transaction->points_earned }} Poin Eco</span>
                        @else
                            <span class="routine-badge">Setoran Rutin</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <span aria-hidden="true">♻</span>
                    <strong>Belum ada riwayat setoran</strong>
                    <p>Setoran warga akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </section>

    <a class="button button--primary button--full download-button" href="{{ route('riwayat.pdf') }}">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v4h14v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Unduh Laporan Bulanan (PDF)
    </a>
@endsection
