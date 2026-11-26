@extends('layouts.app')

@section('title', 'Neon Store')

@section('content')
<section class="rounded-2xl border border-zinc-800 bg-[image:radial-gradient(ellipse_at_top,rgba(34,211,238,0.12),transparent_60%)] px-6 py-16 text-center">
    <h1 class="text-4xl font-bold text-cyan-300 drop-shadow-[0_0_20px_rgba(34,211,238,0.8)] sm:text-6xl">Neon Sign Premium</h1>
    <p class="mx-auto mt-4 max-w-xl text-zinc-400">
        Lampu neon untuk ruangan, bisnis, dan hadiah. Pilih produk siap pakai dari katalog kami.
    </p>
    <a href="{{ route('catalog.index') }}"
        class="mt-8 inline-block rounded-md border border-pink-500 px-6 py-3 text-pink-400 shadow-[0_0_16px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
        Lihat katalog
    </a>
</section>

@if ($categories->isNotEmpty())
    <section class="mt-12">
        <h2 class="mb-4 text-xl font-semibold text-zinc-100">Kategori</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ($categories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                    class="rounded-full border border-zinc-700 px-4 py-2 text-sm text-zinc-300 hover:border-cyan-400 hover:text-cyan-300">
                    {{ $category->name }}
                    <span class="text-zinc-500">({{ $category->active_products_count }})</span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<section class="mt-12">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-zinc-100">Produk terbaru</h2>
        <a href="{{ route('catalog.index') }}" class="text-sm text-cyan-300 hover:underline">Lihat semua</a>
    </div>

    @if ($latestProducts->isEmpty())
        <p class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center text-zinc-500">Belum ada produk.</p>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($latestProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
</section>
@endsection
