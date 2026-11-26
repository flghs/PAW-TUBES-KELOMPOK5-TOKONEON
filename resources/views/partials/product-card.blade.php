<a href="{{ route('catalog.show', $product->slug) }}"
    class="group block overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900 transition hover:border-pink-500/60 hover:shadow-[0_0_24px_rgba(236,72,153,0.35)]">
    @if ($product->imageUrl())
        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover" loading="lazy">
    @else
        <div class="flex aspect-square w-full items-center justify-center bg-zinc-800 text-sm text-zinc-500">Tanpa foto</div>
    @endif

    <div class="p-4">
        <p class="text-xs uppercase tracking-wide text-pink-400">{{ $product->category->name }}</p>
        <h3 class="mt-1 font-semibold text-zinc-100 group-hover:text-cyan-300">{{ $product->name }}</h3>
        <p class="mt-2 text-cyan-300">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        @if ($product->stock < 1)
            <p class="mt-1 text-xs text-red-400">Stok habis</p>
        @endif
    </div>
</a>
