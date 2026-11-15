@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <form method="get" action="{{ route('notifikasi') }}">
        <label for="type">Jenis</label>
        <select id="type" name="type">
            <option value="">Semua jenis</option>
            @foreach (['warning', 'info'] as $option)
                <option value="{{ $option }}" @selected($type === $option)>{{ ucfirst($option) }}</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
    </form>
    <div class="grid">
        @forelse ($notifications as $notification)
            <article class="card">
                <p class="muted">{{ ucfirst($notification->type) }} · {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                <h2>{{ $notification->title }}</h2>
                <p>{{ $notification->message }}</p>
                <p>{{ $notification->is_resolved ? 'Selesai' : 'Aktif' }} · {{ $notification->is_read ? 'Dibaca' : 'Belum dibaca' }}</p>
            </article>
        @empty
            <p>Belum ada notifikasi.</p>
        @endforelse
    </div>
@endsection
