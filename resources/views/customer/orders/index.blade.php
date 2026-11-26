@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Pesanan saya</h1>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Nomor</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3">
                        <a href="{{ route('customer.orders.show', $order) }}" class="text-cyan-300 hover:underline">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-zinc-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @include('partials.order-status', ['order' => $order])
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-zinc-500">Belum ada pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.pager', ['paginator' => $orders])
@endsection
