@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Keranjang</h1>

@if ($items->isEmpty())
    <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-10 text-center">
        <p class="text-zinc-400">Keranjangmu masih kosong.</p>
        <a href="{{ route('catalog.index') }}" class="mt-4 inline-block text-cyan-300 hover:underline">Lihat katalog</a>
    </div>
@else
    <div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-800 text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Subtotal</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php($product = $item['product'])
                    <tr class="border-b border-zinc-800 last:border-0">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($product->imageUrl())
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-14 w-14 rounded object-cover">
                                @else
                                    <div class="flex h-14 w-14 items-center justify-center rounded bg-zinc-800 text-xs text-zinc-500">-</div>
                                @endif
                                <a href="{{ route('catalog.show', $product->slug) }}" class="text-zinc-100 hover:text-cyan-300">{{ $product->name }}</a>
                            </div>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('cart.update', $product->slug) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" min="1" max="{{ min($product->stock, 100) }}" value="{{ $item['quantity'] }}" required
                                    class="w-20 rounded-md border border-zinc-700 bg-zinc-950 px-2 py-1 focus:border-cyan-400 focus:outline-none">
                                <button type="submit" class="text-xs text-cyan-300 hover:underline">Ubah</button>
                            </form>
                            <p class="mt-1 text-xs text-zinc-500">Stok {{ $product->stock }}</p>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('cart.destroy', $product->slug) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex items-center justify-end gap-6">
        <p class="text-zinc-400">Total</p>
        <p class="text-2xl font-semibold text-cyan-300">Rp {{ number_format($total, 0, ',', '.') }}</p>
    </div>
@endif
@endsection
