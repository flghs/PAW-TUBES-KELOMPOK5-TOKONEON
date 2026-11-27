@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<a href="{{ route('catalog.show', $product->slug) }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Kembali ke produk</a>

<h1 class="mt-6 text-2xl font-semibold text-cyan-300">Checkout</h1>

<div class="mt-6 grid gap-8 lg:grid-cols-3">
    <form method="POST" action="{{ route('checkout.store', $product->slug) }}"
        class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 lg:col-span-2">
        @csrf
        <input type="hidden" name="quantity" value="{{ old('quantity', $quantity) }}">
        @error('quantity')
            <p class="text-sm text-red-400">{{ $message }}</p>
        @enderror

        <h2 class="text-lg font-semibold text-zinc-100">Alamat pengiriman</h2>

        <div>
            <label for="recipient_name" class="mb-1 block text-sm">Nama penerima</label>
            <input id="recipient_name" name="recipient_name" type="text" value="{{ old('recipient_name') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('recipient_name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="mb-1 block text-sm">Nomor telepon / WhatsApp</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            <p class="mt-1 text-xs text-zinc-500">Dipakai untuk melacak pesanan dan dihubungi tim kami.</p>
            @error('phone')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="shipping_address" class="mb-1 block text-sm">Alamat lengkap</label>
            <textarea id="shipping_address" name="shipping_address" rows="3" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('shipping_address') }}</textarea>
            @error('shipping_address')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="city" class="mb-1 block text-sm">Kota</label>
                <input id="city" name="city" type="text" value="{{ old('city') }}" required
                    class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                @error('city')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="province" class="mb-1 block text-sm">Provinsi</label>
                <input id="province" name="province" type="text" value="{{ old('province') }}" required
                    class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                @error('province')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="postal_code" class="mb-1 block text-sm">Kode pos</label>
                <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}" required
                    class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                @error('postal_code')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="notes" class="mb-1 block text-sm">Catatan (opsional)</label>
            <textarea id="notes" name="notes" rows="2"
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="rounded-md bg-pink-500 px-4 py-2 font-semibold text-white shadow-[0_0_16px_rgba(236,72,153,0.6)] hover:bg-pink-400">
            Buat pesanan
        </button>
    </form>

    <div class="h-fit rounded-xl border border-zinc-800 bg-zinc-900 p-6">
        <h2 class="text-lg font-semibold text-zinc-100">Ringkasan</h2>

        @if ($product->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="mt-4 w-full rounded-md object-cover">
        @endif

        <p class="mt-4 font-semibold text-cyan-300">{{ $product->name }}</p>
        <p class="mt-1 text-sm text-zinc-400">
            {{ $quantity }} x Rp {{ number_format($product->price, 0, ',', '.') }}
        </p>

        <div class="mt-4 border-t border-zinc-800 pt-4">
            <p class="text-sm text-zinc-400">Total</p>
            <p class="text-2xl font-semibold text-zinc-100">Rp {{ number_format($product->price * $quantity, 0, ',', '.') }}</p>
        </div>

        <p class="mt-4 text-xs text-zinc-500">Pembayaran dilakukan lewat transfer setelah pesanan dibuat. Tidak perlu membuat akun.</p>
        <p class="mt-2 text-xs text-zinc-500">Stok dipastikan setelah pembayaranmu dikonfirmasi. Kalau stok sudah diambil pesanan lain lebih dulu, tim kami akan menghubungimu lewat WhatsApp untuk pengembalian dana atau menunggu barang dibuat.</p>
    </div>
</div>
@endsection
