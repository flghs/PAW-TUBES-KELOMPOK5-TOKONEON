@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
<div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8">
    <h1 class="text-2xl font-semibold text-cyan-300">Halo, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-zinc-400">Pesanan, request custom, dan chat akan ditambahkan di fase berikutnya.</p>
</div>
@endsection
