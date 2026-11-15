<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Smart Eco-RT</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1, p { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #777; padding: 7px; text-align: left; }
        th { background: #e8f2eb; }
    </style>
</head>
<body>
    <h1>Laporan Setoran Smart Eco-RT</h1>
    <p>{{ now()->translatedFormat('F Y') }}</p>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>Warga</th><th>Kategori</th><th>Jumlah</th><th>Poin</th></tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->citizen_name }}</td>
                    <td>{{ ucfirst($transaction->category) }}</td>
                    <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->unit }}</td>
                    <td>{{ $transaction->points_earned }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada transaksi pada bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
