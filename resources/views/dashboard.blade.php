@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8">
    <h1 class="text-2xl font-semibold text-cyan-300">Halo, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-zinc-400">Kamu masuk sebagai <span class="font-semibold text-pink-400">{{ auth()->user()->role }}</span>.</p>
</div>
@endsection
