@extends('layouts.app')

@section('title', 'Katalog')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Katalog produk</h1>

<form method="GET" action="{{ route('catalog.index') }}" class="mb-8 flex flex-col gap-3 sm:flex-row">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama produk"
        class="w-full rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 focus:border-cyan-400 focus:outline-none sm:max-w-xs">

    <select name="category"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        <option value="">Semua kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Cari
    </button>

    @if (! empty($filters['q']) || ! empty($filters['category']))
        <a href="{{ route('catalog.index') }}" class="self-center text-sm text-zinc-400 hover:text-zinc-200">Reset</a>
    @endif
</form>

@if ($products->isEmpty())
    <p class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center text-zinc-500">
        Tidak ada produk yang cocok.
    </p>
@else
    <p class="mb-4 text-sm text-zinc-500">{{ $products->total() }} produk</p>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach ($products as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>

    @include('partials.pager', ['paginator' => $products])
@endif
@endsection
