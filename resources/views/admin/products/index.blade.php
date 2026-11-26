@extends('layouts.panel')

@section('title', 'Produk')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-cyan-300">Produk</h1>
    <a href="{{ route('admin.products.create') }}"
        class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
        Tambah produk
    </a>
</div>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Stok</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3">
                        @if ($product->imageUrl())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-12 w-12 rounded object-cover">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded bg-zinc-800 text-xs text-zinc-500">-</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $product->category->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $product->stock }}</td>
                    <td class="px-4 py-3">
                        @if ($product->is_active)
                            <span class="text-cyan-300">Aktif</span>
                        @else
                            <span class="text-zinc-500">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-cyan-300 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-zinc-500">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div class="mt-4 flex items-center justify-between text-sm text-zinc-400">
        <span>Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}</span>
        <div class="flex gap-4">
            @if ($products->previousPageUrl())
                <a href="{{ $products->previousPageUrl() }}" class="text-cyan-300 hover:underline">Sebelumnya</a>
            @endif
            @if ($products->nextPageUrl())
                <a href="{{ $products->nextPageUrl() }}" class="text-cyan-300 hover:underline">Berikutnya</a>
            @endif
        </div>
    </div>
@endif
@endsection
