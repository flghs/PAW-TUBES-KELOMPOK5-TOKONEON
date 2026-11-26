@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
<div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8">
    <h1 class="text-2xl font-semibold text-cyan-300">Halo, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-zinc-400">Lihat status pesananmu atau pilih neon sign baru.</p>

    <div class="mt-6 flex flex-wrap gap-4">
        <a href="{{ route('customer.orders.index') }}"
            class="rounded-md border border-cyan-400 px-4 py-2 text-sm text-cyan-300 shadow-[0_0_12px_rgba(34,211,238,0.4)] hover:bg-cyan-400/10">
            Pesanan saya
        </a>
        <a href="{{ route('catalog.index') }}"
            class="rounded-md border border-zinc-700 px-4 py-2 text-sm text-zinc-300 hover:border-cyan-400 hover:text-cyan-300">
            Lihat katalog
        </a>
        <a href="{{ route('custom.index') }}"
            class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
            Pesan custom
        </a>
    </div>
</div>
@endsection
