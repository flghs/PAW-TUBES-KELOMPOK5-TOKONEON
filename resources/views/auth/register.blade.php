@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="mx-auto max-w-md rounded-xl border border-zinc-800 bg-zinc-900 p-8 shadow-[0_0_30px_rgba(236,72,153,0.15)]">
    <h1 class="mb-6 text-2xl font-semibold text-pink-400">Daftar</h1>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm">Nama</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-pink-400 focus:outline-none">
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-pink-400 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="mb-1 block text-sm">Nomor telepon (opsional)</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-pink-400 focus:outline-none">
            @error('phone')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm">Password (minimal 8 karakter)</label>
            <input id="password" name="password" type="password" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-pink-400 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm">Ulangi password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-pink-400 focus:outline-none">
        </div>

        <button type="submit"
            class="w-full rounded-md bg-pink-500 px-4 py-2 font-semibold text-white shadow-[0_0_16px_rgba(236,72,153,0.6)] hover:bg-pink-400">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-sm text-zinc-400">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-cyan-300 hover:underline">Masuk</a>
    </p>
</div>
@endsection
