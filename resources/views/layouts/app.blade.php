<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Neon Store')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
    <nav class="border-b border-zinc-800">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="/" class="text-xl font-bold text-cyan-300 drop-shadow-[0_0_8px_rgba(34,211,238,0.9)]">Neon Store</a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-cyan-300">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-pink-400">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-cyan-300">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-md border border-pink-500 px-3 py-1 text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-4 py-10">
        @yield('content')
    </main>
</body>
</html>
