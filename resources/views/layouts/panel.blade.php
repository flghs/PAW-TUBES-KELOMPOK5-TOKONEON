<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') - Neon Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
    @php($user = auth()->user())

    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="w-full shrink-0 border-b border-zinc-800 bg-zinc-900 p-4 md:w-60 md:border-b-0 md:border-r">
            <a href="{{ route('dashboard') }}" class="block text-xl font-bold text-cyan-300 drop-shadow-[0_0_8px_rgba(34,211,238,0.9)]">Neon Store</a>
            <p class="mt-1 text-xs uppercase tracking-widest text-pink-400">{{ $user->role }}</p>

            <nav class="mt-6 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}"
                    class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.dashboard', 'staff.dashboard') ? 'bg-cyan-400/10 text-cyan-300' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    Dashboard
                </a>

                <a href="{{ route('staff.custom-requests.index') }}"
                    class="block rounded-md px-3 py-2 {{ request()->routeIs('staff.custom-requests.*') ? 'bg-cyan-400/10 text-cyan-300' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    Request custom
                </a>

                @if ($user->isAdmin())
                    <a href="{{ route('admin.products.index') }}"
                        class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.products.*') ? 'bg-cyan-400/10 text-cyan-300' : 'text-zinc-300 hover:bg-zinc-800' }}">
                        Produk
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.categories.*') ? 'bg-cyan-400/10 text-cyan-300' : 'text-zinc-300 hover:bg-zinc-800' }}">
                        Kategori
                    </a>
                @endif
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="text-sm text-zinc-400 hover:text-pink-400">Keluar</button>
            </form>
        </aside>

        <main class="flex-1 p-6 md:p-10">
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
    </div>
</body>
</html>
