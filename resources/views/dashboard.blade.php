@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <p>Total berat sampah: <strong>{{ number_format((float) $totalWeight, 2) }} kg/L</strong></p>
    <p>Perangkat: <strong>{{ $device?->name ?? 'Belum terdaftar' }}</strong> —
        {{ $isOnline ? 'Online' : 'Offline' }}</p>
    <div class="grid">
        @foreach ($bins as $bin)
            <article class="card">
                <h2>{{ $bin->name }}</h2>
                <p>{{ $bin->subtitle }}</p>
                <p>{{ number_format($bin->current_weight, 2) }} {{ $bin->unit }}</p>
                <p>Kapasitas {{ $bin->capacity_percent }}% · {{ str_replace('_', ' ', $bin->status) }}</p>
            </article>
        @endforeach
    </div>
@endsection
