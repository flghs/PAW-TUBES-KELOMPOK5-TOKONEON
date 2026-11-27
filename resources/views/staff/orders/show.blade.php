@extends('layouts.panel')

@section('title', 'Pesanan ' . $order->order_number)

@section('content')
@php
    $waMessage = "Halo {$order->recipient_name}, kami dari Neon Store tentang pesanan {$order->order_number}.";
    $waUrl = 'https://wa.me/' . $order->whatsappPhone() . '?text=' . rawurlencode($waMessage);
@endphp

<a href="{{ route('staff.orders.index') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Daftar pesanan</a>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <h1 class="text-2xl font-semibold text-cyan-300">{{ $order->order_number }}</h1>
    @include('partials.order-status', ['order' => $order])
</div>
<p class="mt-1 text-sm text-zinc-500">
    {{ $order->typeLabel() }} &middot; dibuat {{ $order->created_at->format('d/m/Y H:i') }}
    @if ($order->handler)
        &middot; ditangani oleh {{ $order->handler->name }}
    @endif
</p>

@if ($order->stock_short && $order->status === 'paid')
    <div class="mt-6 space-y-3 rounded-xl border border-yellow-500/40 bg-yellow-500/10 p-6 text-sm text-yellow-200">
        <h2 class="text-lg font-semibold text-yellow-300">Stok kurang, hubungi pembeli</h2>
        <p>
            Pesanan ini sudah lunas, tetapi stok sudah diambil pesanan lain yang dikonfirmasi lebih dulu.
            Tanyakan ke pembeli lewat WhatsApp:
        </p>
        <ul class="list-disc space-y-1 pl-5">
            <li>Pilih uang kembali: klik <strong>Batalkan pesanan</strong>, lalu kembalikan uangnya secara manual.</li>
            <li>Pilih menunggu: ubah status ke <strong>Diproduksi</strong>.</li>
        </ul>
        @foreach ($order->items as $item)
            @if ($item->product)
                <p class="text-xs text-yellow-200/80">
                    {{ $item->item_name }}: dipesan {{ $item->quantity }}, stok sekarang {{ $item->product->stock }}.
                </p>
            @endif
        @endforeach
        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
            class="inline-block rounded-md border border-green-500 px-4 py-2 text-green-300 hover:bg-green-500/10">
            Chat pembeli lewat WhatsApp
        </a>
    </div>
@endif

<div class="mt-6 grid gap-8 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-800 text-zinc-400">
                    <tr>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-b border-zinc-800 last:border-0">
                            <td class="px-4 py-3">{{ $item->item_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-zinc-800">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-zinc-400">Total</td>
                        <td class="px-4 py-3 text-right text-lg font-semibold text-cyan-300">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <dl class="space-y-3 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
            <div>
                <dt class="text-zinc-500">Pembeli</dt>
                <dd class="mt-1 text-zinc-100">
                    {{ $order->recipient_name ?: '-' }} ({{ $order->phone ?: '-' }})
                    @if ($order->phone)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="ml-2 text-green-300 hover:underline">Chat WhatsApp</a>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-zinc-500">Alamat</dt>
                <dd class="mt-1 text-zinc-100">
                    @if ($order->shipping_address)
                        {{ $order->shipping_address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
                    @else
                        -
                    @endif
                </dd>
            </div>
            @if ($order->notes)
                <div>
                    <dt class="text-zinc-500">Catatan pembeli</dt>
                    <dd class="mt-1 text-zinc-100">{{ $order->notes }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="space-y-6">
        <div class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
            <h2 class="text-lg font-semibold text-zinc-100">Pembayaran</h2>

            @if ($order->payment_status === 'paid')
                <p class="text-green-300">
                    Lunas{{ $order->paid_at ? ' pada ' . $order->paid_at->format('d/m/Y H:i') : '' }}.
                </p>
            @elseif ($order->payment_status === 'rejected')
                <p class="text-red-300">Bukti bayar ditolak. Menunggu pembeli mengunggah ulang.</p>
            @elseif ($order->payment_status === 'pending_review')
                <p class="text-cyan-200">Bukti bayar menunggu pemeriksaan. Cocokkan jumlah dan nama pengirim dengan mutasi rekening.</p>
            @else
                <p class="text-zinc-400">Belum ada bukti bayar.</p>
            @endif

            @if ($order->payment_proof_path)
                <a href="{{ route('orders.proof', $order->order_number) }}" target="_blank">
                    <img src="{{ route('orders.proof', $order->order_number) }}" alt="Bukti bayar" class="w-64 rounded-md border border-zinc-700">
                </a>
            @endif

            @if ($order->status === 'pending_payment' && $order->payment_status === 'pending_review')
                <div class="flex flex-wrap items-center gap-4">
                    <form method="POST" action="{{ route('staff.orders.payment.approve', $order) }}"
                        onsubmit="return confirm('Konfirmasi pembayaran ini sudah diterima? Stok produk akan dikurangi.')">
                        @csrf
                        <button type="submit"
                            class="rounded-md bg-green-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-green-300">
                            Setujui pembayaran
                        </button>
                    </form>

                    <form method="POST" action="{{ route('staff.orders.payment.reject', $order) }}"
                        onsubmit="return confirm('Tolak bukti bayar ini?')">
                        @csrf
                        <button type="submit" class="text-red-400 hover:underline">Tolak bukti</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
            <h2 class="text-lg font-semibold text-zinc-100">Status pesanan</h2>

            @if ($order->status === 'cancelled')
                <p class="text-zinc-400">Pesanan ini dibatalkan.</p>
            @elseif ($order->status === 'pending_payment')
                <p class="text-zinc-400">Status bisa diubah setelah pembayaran disetujui.</p>
            @elseif ($order->status === 'completed')
                <p class="text-green-300">Pesanan selesai.</p>
            @endif

            @if ($order->nextStatuses())
                <form method="POST" action="{{ route('staff.orders.status', $order) }}" class="flex flex-wrap items-center gap-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" required
                        class="rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                        @foreach ($order->nextStatuses() as $next)
                            <option value="{{ $next }}">{{ \App\Models\Order::STATUS_LABELS[$next] }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
                        Ubah status
                    </button>
                </form>
                @error('status')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            @endif

            @if ($order->canBeCancelled())
                <form method="POST" action="{{ route('staff.orders.cancel', $order) }}"
                    onsubmit="return confirm('Batalkan pesanan ini?{{ $order->stock_deducted ? ' Stok akan dikembalikan.' : '' }}')">
                    @csrf
                    <button type="submit" class="text-red-400 hover:underline">Batalkan pesanan</button>
                    @if ($order->payment_status === 'paid')
                        <p class="mt-1 text-xs text-zinc-500">Pesanan sudah dibayar, jadi uang pembeli perlu dikembalikan secara manual.</p>
                    @endif
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
