<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') - Neon Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="w-full shrink-0 border-b border-zinc-800 bg-zinc-900 p-4 md:w-60 md:border-b-0 md:border-r">
            <a href="{{ route('dashboard') }}" class="block text-xl font-bold text-cyan-300 drop-shadow-[0_0_8px_rgba(34,211,238,0.9)]">Neon Store</a>
            <p class="mt-1 text-xs uppercase tracking-widest text-pink-400">{{ auth()->user()->role }}</p>

            <nav class="mt-6 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}" class="block rounded-md bg-cyan-400/10 px-3 py-2 text-cyan-300">Dashboard</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="text-sm text-zinc-400 hover:text-pink-400">Keluar</button>
            </form>
        </aside>

        <main class="flex-1 p-6 md:p-10">
            @yield('content')
        </main>
    </div>
</body>
</html>
