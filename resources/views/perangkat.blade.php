@extends('layouts.app')

@section('title', 'Perangkat')

@section('content')
    @if ($device)
        <div class="card">
            <h2>{{ $device->name }}</h2>
            <p>Hub: {{ $device->hub_id }}</p>
            <p>Lokasi: {{ $device->location }}</p>
            <p>Wi-Fi: {{ $device->wifi_ssid }}</p>
            <p>Baterai: {{ $device->battery_percent }}%</p>
            <p>Status: {{ $isOnline ? 'Online' : 'Offline' }}</p>
            <p>Terakhir terlihat: {{ $device->last_seen_at?->format('d/m/Y H:i:s') ?? 'Belum ada data' }}</p>
        </div>
    @else
        <p>Belum ada perangkat terdaftar.</p>
    @endif
@endsection
