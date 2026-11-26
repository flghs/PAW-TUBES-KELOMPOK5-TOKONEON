@extends('layouts.panel')

@section('title', 'Ubah Kategori')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Ubah kategori</h1>

<form method="POST" action="{{ route('admin.categories.update', $category) }}"
    class="max-w-lg space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @method('PUT')
    @include('admin.categories._form')
</form>
@endsection
