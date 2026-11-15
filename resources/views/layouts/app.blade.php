<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Smart Eco-RT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; color: #1f2937; background: #f3f6f4; }
        header, main { max-width: 1080px; margin: auto; padding: 1.25rem; }
        header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        nav { display: flex; flex-wrap: wrap; gap: 1rem; }
        a { color: #16764a; }
        main { background: white; border-radius: .75rem; margin-bottom: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
        .card { border: 1px solid #dce5df; border-radius: .5rem; padding: 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .65rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
        form { display: inline-flex; gap: .5rem; margin: .5rem 0; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <header>
        <strong>Smart Eco-RT</strong>
        <nav aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('riwayat') }}">Riwayat</a>
            <a href="{{ route('notifikasi') }}">Notifikasi ({{ $unreadCount ?? 0 }})</a>
            <a href="{{ route('perangkat') }}">Perangkat</a>
        </nav>
    </header>
    <main>
        <h1>@yield('title')</h1>
        @yield('content')
    </main>
</body>
</html>
