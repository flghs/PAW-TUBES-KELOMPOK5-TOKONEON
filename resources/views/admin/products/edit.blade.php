@extends('layouts.panel')

@section('title', 'Ubah Produk')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Ubah produk</h1>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
    class="max-w-2xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @method('PUT')
    @include('admin.products._form')
</form>
@endsection
