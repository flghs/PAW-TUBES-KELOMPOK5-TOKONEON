@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="mx-auto max-w-md rounded-xl border border-zinc-800 bg-zinc-900 p-8 shadow-[0_0_30px_rgba(34,211,238,0.15)]">
    <h1 class="mb-2 text-2xl font-semibold text-cyan-300">Lacak pesanan</h1>
    <p class="mb-6 text-sm text-zinc-400">Masukkan nomor pesanan dan nomor telepon yang kamu pakai saat memesan.</p>

    <form method="POST" action="{{ route('orders.lookup') }}" class="space-y-4">
        @csrf

        <div>
            <label for="order_number" class="mb-1 block text-sm">Nomor pesanan</label>
            <input id="order_number" name="order_number" type="text" value="{{ old('order_number') }}" required
                placeholder="ORD-20261127-ABCDE"
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 uppercase focus:border-cyan-400 focus:outline-none">
            @error('order_number')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="mb-1 block text-sm">Nomor telepon</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('phone')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
            Lihat pesanan
        </button>
    </form>
</div>
@endsection
