@extends('layouts.panel')

@section('title', 'Tambah Kategori')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Tambah kategori</h1>

<form method="POST" action="{{ route('admin.categories.store') }}"
    class="max-w-lg space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @include('admin.categories._form')
</form>
@endsection
