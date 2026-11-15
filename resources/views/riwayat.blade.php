@extends('layouts.app')

@section('title', 'Riwayat Setoran')

@section('content')
    <p>{{ $weeklyCount }} transaksi dalam 7 hari terakhir.</p>
    <p><a href="{{ route('riwayat.pdf') }}">Unduh laporan bulan ini (PDF)</a></p>
    <form method="get" action="{{ route('riwayat') }}">
        <label for="category">Kategori</label>
        <select id="category" name="category">
            <option value="">Semua kategori</option>
            @foreach (['organik', 'anorganik', 'jelantah'] as $option)
                <option value="{{ $option }}" @selected($category === $option)>{{ ucfirst($option) }}</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
    </form>
    <table>
        <thead>
            <tr><th>Waktu</th><th>Warga</th><th>Kategori</th><th>Jumlah</th><th>Poin</th></tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->citizen_name }}</td>
                    <td>{{ ucfirst($transaction->category) }}</td>
                    <td>{{ $transaction->amount }} {{ $transaction->unit }}</td>
                    <td>{{ $transaction->points_earned }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada riwayat setoran.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
