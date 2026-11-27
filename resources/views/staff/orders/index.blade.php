@extends('layouts.panel')

@section('title', 'Pesanan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-cyan-300">Pesanan</h1>
    <p class="mt-1 text-sm text-zinc-400">Pesanan custom dicatat lewat WhatsApp, tidak lewat website.</p>
</div>

<form method="GET" action="{{ route('staff.orders.index') }}" class="mb-6 flex flex-col gap-3 lg:flex-row">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor, nama, atau telepon"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">

    <select name="status"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
        <option value="">Semua status pesanan</option>
        @foreach (\App\Models\Order::STATUS_LABELS as $value => $label)
            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>

    <select name="payment_status"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
        <option value="">Semua status pembayaran</option>
        @foreach (\App\Models\Order::PAYMENT_LABELS as $value => $label)
            <option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>

    <label class="flex items-center gap-2 text-sm text-zinc-300">
        <input type="checkbox" name="attention" value="1" @checked(! empty($filters['attention'])) class="rounded border-zinc-700">
        Perlu dihubungi (stok kurang)
    </label>

    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 text-sm font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Filter
    </button>

    @if (! empty($filters['q']) || ! empty($filters['status']) || ! empty($filters['payment_status']) || ! empty($filters['attention']))
        <a href="{{ route('staff.orders.index') }}" class="self-center text-sm text-zinc-400 hover:text-zinc-200">Reset</a>
    @endif
</form>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Nomor</th>
                <th class="px-4 py-3">Pembeli</th>
                <th class="px-4 py-3">Jenis</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3">
                        <a href="{{ route('staff.orders.show', $order) }}" class="text-cyan-300 hover:underline">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        {{ $order->recipient_name ?: '-' }}
                        <span class="block text-xs text-zinc-500">{{ $order->phone ?: '-' }}</span>
                    </td>
                    <td class="px-4 py-3 text-zinc-400">{{ $order->typeLabel() }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @include('partials.order-status', ['order' => $order])
                        </div>
                    </td>
                    <td class="px-4 py-3 text-zinc-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-zinc-500">Tidak ada pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.pager', ['paginator' => $orders])
@endsection
