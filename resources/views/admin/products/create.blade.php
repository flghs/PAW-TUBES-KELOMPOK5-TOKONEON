@extends('layouts.panel')

@section('title', 'Tambah Produk')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Tambah produk</h1>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
    class="max-w-2xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @include('admin.products._form')
</form>
@endsection
