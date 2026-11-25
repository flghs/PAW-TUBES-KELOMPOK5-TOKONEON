@extends('layouts.panel')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-2xl font-semibold text-cyan-300">Dashboard Admin</h1>
<p class="mt-2 text-zinc-400">Halo, {{ auth()->user()->name }}. Statistik dan menu pengelolaan akan ditambahkan di fase berikutnya.</p>
@endsection
