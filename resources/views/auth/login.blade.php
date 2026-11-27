@extends('layouts.app')

@section('title', 'Masuk Staff')

@section('content')
<div class="mx-auto max-w-md rounded-xl border border-zinc-800 bg-zinc-900 p-8 shadow-[0_0_30px_rgba(34,211,238,0.15)]">
    <h1 class="mb-2 text-2xl font-semibold text-cyan-300">Masuk staff</h1>
    <p class="mb-6 text-sm text-zinc-400">Halaman ini hanya untuk staff dan admin. Pembeli tidak perlu masuk.</p>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1" class="rounded border-zinc-700">
            Ingat saya
        </label>

        <button type="submit"
            class="w-full rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
            Masuk
        </button>
    </form>
</div>
@endsection
