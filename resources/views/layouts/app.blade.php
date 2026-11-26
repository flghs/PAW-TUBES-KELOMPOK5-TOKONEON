<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Neon Store')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-zinc-950 text-zinc-100 antialiased">
    @php($showCustomLink = ! auth()->check() || auth()->user()->isCustomer())

    <nav class="border-b border-zinc-800">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-xl font-bold text-cyan-300 drop-shadow-[0_0_8px_rgba(34,211,238,0.9)]">Neon Store</a>
                <div class="hidden items-center gap-6 text-sm sm:flex">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-cyan-300' : 'text-zinc-300 hover:text-cyan-300' }}">Beranda</a>
                    <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'text-cyan-300' : 'text-zinc-300 hover:text-cyan-300' }}">Katalog</a>
                    @if ($showCustomLink)
                        <a href="{{ route('customer.custom-requests.create') }}" class="{{ request()->routeIs('customer.custom-requests.*') ? 'text-cyan-300' : 'text-zinc-300 hover:text-cyan-300' }}">Custom Neon</a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-cyan-300">Akun saya</a>
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

        <div class="flex gap-6 border-t border-zinc-900 px-4 py-2 text-sm sm:hidden">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-cyan-300' : 'text-zinc-300' }}">Beranda</a>
            <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'text-cyan-300' : 'text-zinc-300' }}">Katalog</a>
            @if ($showCustomLink)
                <a href="{{ route('customer.custom-requests.create') }}" class="{{ request()->routeIs('customer.custom-requests.*') ? 'text-cyan-300' : 'text-zinc-300' }}">Custom Neon</a>
            @endif
        </div>
    </nav>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-md border border-cyan-500/40 bg-cyan-500/10 px-4 py-3 text-sm text-cyan-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-md border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-zinc-800 py-6 text-center text-sm text-zinc-500">
        &copy; {{ date('Y') }} Neon Store
    </footer>
</body>
</html>
