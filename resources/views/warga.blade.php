@extends('layouts.app')

@section('title', 'Data Warga')

@section('content')
    @if (session('status'))
        <div class="flash-message" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash-message flash-message--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="content-card resident-create-card">
        <div class="list-heading resident-create-heading">
            <div><p class="eyebrow">PENGELOLAAN WARGA</p><h2>Tambah Warga</h2></div>
            <span class="count-chip">{{ $residents->count() }} warga terdaftar</span>
        </div>
        <form class="resident-create-form" method="post" action="{{ route('warga.store') }}">
            @csrf
            <div class="resident-field">
                <label class="field-label" for="resident-name">Nama lengkap</label>
                <input class="text-input" id="resident-name" name="name" value="{{ old('name') }}" maxlength="255" placeholder="Nama warga" required>
            </div>
            <div class="resident-field">
                <label class="field-label" for="resident-phone">Nomor telepon</label>
                <input class="text-input" id="resident-phone" name="phone" type="tel" value="{{ old('phone') }}" maxlength="20" placeholder="08xxxxxxxxxx" required>
            </div>
            <button class="button button--primary resident-add-button" type="submit">Tambah Warga</button>
        </form>
    </section>

    <section class="section-block">
        <div class="section-heading">
            <div><p class="eyebrow">DATA &amp; SALDO POIN</p><h2>Daftar Warga</h2></div>
        </div>
        <div class="resident-list">
            @forelse ($residents as $resident)
                <article class="resident-card content-card">
                    <div class="resident-card__heading">
                        <span class="resident-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($resident->name, 0, 1)) }}</span>
                        <div class="resident-card__identity">
                            <h3>{{ $resident->name }}</h3>
                            <span>{{ $resident->phone }}</span>
                        </div>
                        <div class="resident-balance">
                            <strong>{{ number_format($resident->points_balance) }}</strong>
                            <span>Poin Eco</span>
                        </div>
                    </div>
                    <form class="redeem-form" method="post" action="{{ route('warga.redeem', $resident) }}">
                        @csrf
                        <label class="visually-hidden" for="points-{{ $resident->id }}">Jumlah poin untuk ditukar</label>
                        <input class="text-input" id="points-{{ $resident->id }}" name="points" type="number" min="1" max="{{ $resident->points_balance }}" placeholder="Jumlah poin" required @if ($resident->points_balance < 1) disabled @endif>
                        <button class="button button--secondary" type="submit" @if ($resident->points_balance < 1) disabled @endif>Tukar Poin</button>
                    </form>
                </article>
            @empty
                <div class="content-card empty-state">
                    <span aria-hidden="true">♙</span>
                    <strong>Belum ada warga terdaftar</strong>
                    <p>Tambahkan warga untuk menghubungkan klaim setoran dengan saldo poin.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
