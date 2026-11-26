@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
<div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8">
    <h1 class="text-2xl font-semibold text-cyan-300">Halo, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-zinc-400">Mau pesan neon sign dengan desainmu sendiri?</p>

    <div class="mt-6 flex flex-wrap gap-4">
        <a href="{{ route('customer.custom-requests.create') }}"
            class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
            Buat request custom
        </a>
        <a href="{{ route('customer.custom-requests.index') }}"
            class="rounded-md border border-zinc-700 px-4 py-2 text-sm text-zinc-300 hover:border-cyan-400 hover:text-cyan-300">
            Request saya
        </a>
    </div>
</div>
@endsection
