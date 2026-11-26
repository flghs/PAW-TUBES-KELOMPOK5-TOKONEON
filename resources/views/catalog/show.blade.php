@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php($canBuy = ! auth()->check() || auth()->user()->isCustomer())

<a href="{{ route('catalog.index') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Kembali ke katalog</a>

<div class="mt-6 grid gap-8 md:grid-cols-2">
    <div>
        @if ($product->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                class="w-full rounded-xl border border-zinc-800 object-cover shadow-[0_0_30px_rgba(34,211,238,0.15)]">
        @else
            <div class="flex aspect-square w-full items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900 text-zinc-500">
                Tanpa foto
            </div>
        @endif
    </div>

    <div>
        <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}"
            class="text-xs uppercase tracking-wide text-pink-400 hover:underline">
            {{ $product->category->name }}
        </a>

        <h1 class="mt-2 text-3xl font-semibold text-cyan-300">{{ $product->name }}</h1>

        <p class="mt-4 text-2xl text-zinc-100">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

        <p class="mt-2 text-sm">
            @if ($product->stock > 0)
                <span class="text-cyan-300">Stok tersedia ({{ $product->stock }})</span>
            @else
                <span class="text-red-400">Stok habis</span>
            @endif
        </p>

        @if ($product->stock > 0 && $canBuy)
            <form method="POST" action="{{ route('cart.store', $product->slug) }}" class="mt-6 flex items-center gap-3">
                @csrf
                <input type="number" name="quantity" min="1" max="{{ min($product->stock, 100) }}" value="1" required
                    class="w-24 rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                <button type="submit"
                    class="rounded-md bg-pink-500 px-4 py-2 font-semibold text-white shadow-[0_0_16px_rgba(236,72,153,0.6)] hover:bg-pink-400">
                    Tambah ke keranjang
                </button>
            </form>
            @error('quantity')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        @endif

        @if ($product->description)
            <div class="mt-6 text-zinc-300">{!! nl2br(e($product->description)) !!}</div>
        @endif
    </div>
</div>

@if ($related->isNotEmpty())
    <section class="mt-16">
        <h2 class="mb-4 text-xl font-semibold text-zinc-100">Produk lain di kategori ini</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ($related as $item)
                @include('partials.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif
@endsection
