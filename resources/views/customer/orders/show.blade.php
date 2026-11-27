@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_number)

@section('content')
<a href="{{ route('orders.track') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Lacak pesanan lain</a>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <h1 class="text-2xl font-semibold text-cyan-300">{{ $order->order_number }}</h1>
    @include('partials.order-status', ['order' => $order])
</div>
<p class="mt-1 text-sm text-zinc-500">Dibuat {{ $order->created_at->format('d/m/Y H:i') }}</p>
<p class="mt-1 text-sm text-zinc-400">Simpan nomor pesanan ini. Kamu bisa membukanya lagi lewat menu Lacak Pesanan dengan nomor telepon yang kamu pakai saat memesan.</p>

<div class="mt-6 grid gap-8 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-800 text-zinc-400">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
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
                <dt class="text-zinc-500">Penerima</dt>
                <dd class="mt-1 text-zinc-100">{{ $order->recipient_name }} ({{ $order->phone }})</dd>
            </div>
            <div>
                <dt class="text-zinc-500">Alamat</dt>
                <dd class="mt-1 text-zinc-100">{{ $order->shipping_address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</dd>
            </div>
            @if ($order->notes)
                <div>
                    <dt class="text-zinc-500">Catatan</dt>
                    <dd class="mt-1 text-zinc-100">{{ $order->notes }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="space-y-6">
        @if ($order->status === 'cancelled')
            <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm text-zinc-400">
                Pesanan ini dibatalkan.
            </div>
        @elseif ($order->payment_status === 'paid')
            @if ($order->stock_short && $order->status === 'paid')
                <div class="rounded-xl border border-yellow-500/40 bg-yellow-500/10 p-6 text-sm text-yellow-200">
                    Pembayaranmu sudah diterima, tetapi stok produk ini sudah diambil pesanan lain lebih dulu.
                    Tim kami akan menghubungimu lewat WhatsApp untuk menawarkan pengembalian dana atau menunggu barang dibuat.
                </div>
            @else
                <div class="rounded-xl border border-green-500/40 bg-green-500/10 p-6 text-sm text-green-300">
                    Pembayaran sudah diterima. Status pesananmu akan diperbarui oleh tim kami.
                </div>
            @endif
        @else
            <div class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
                <h2 class="text-lg font-semibold text-zinc-100">Pembayaran</h2>

                <div>
                    <p class="text-zinc-500">Jumlah yang harus dibayar</p>
                    <p class="text-2xl font-semibold text-cyan-300">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>

                @if (config('shop.payment.account_number'))
                    <div class="rounded-md border border-zinc-700 p-4">
                        <p class="text-zinc-500">Transfer ke</p>
                        <p class="mt-1 text-zinc-100">{{ config('shop.payment.bank_name') }}</p>
                        <p class="text-xl font-semibold tracking-wide text-zinc-100">{{ config('shop.payment.account_number') }}</p>
                        <p class="text-zinc-400">a.n. {{ config('shop.payment.account_name') }}</p>
                    </div>
                @else
                    <p class="text-zinc-500">Info rekening belum diatur.</p>
                @endif

                @if (file_exists(public_path('images/qris.png')))
                    <div>
                        <p class="mb-2 text-zinc-500">Atau scan QRIS</p>
                        <img src="{{ asset('images/qris.png') }}" alt="QRIS" class="w-48 rounded-md bg-white p-2">
                    </div>
                @endif

                @if ($order->payment_status === 'pending_review')
                    <div class="rounded-md border border-cyan-500/40 bg-cyan-500/10 p-4 text-cyan-200">
                        Bukti bayarmu sudah terkirim dan sedang diperiksa.
                    </div>
                    @if ($order->payment_proof_path)
                        <img src="{{ route('orders.proof', $order->order_number) }}" alt="Bukti bayar" class="w-48 rounded-md border border-zinc-700">
                    @endif
                @endif

                @if ($order->payment_status === 'rejected')
                    <div class="rounded-md border border-red-500/40 bg-red-500/10 p-4 text-red-300">
                        Bukti bayar sebelumnya ditolak. Silakan unggah bukti yang benar.
                    </div>
                @endif

                @if ($order->isAwaitingPayment())
                    <form method="POST" action="{{ route('orders.proof.store', $order->order_number) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label for="proof" class="block text-zinc-300">Unggah bukti bayar (jpg, png, atau webp, maksimal 2 MB)</label>
                        <input id="proof" name="proof" type="file" accept="image/jpeg,image/png,image/webp" required
                            class="block w-full text-sm text-zinc-300 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-800 file:px-4 file:py-2 file:text-zinc-200 hover:file:bg-zinc-700">
                        @error('proof')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                            class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
                            Kirim bukti bayar
                        </button>
                    </form>

                    <form method="POST" action="{{ route('orders.cancel', $order->order_number) }}"
                        onsubmit="return confirm('Batalkan pesanan ini?')">
                        @csrf
                        <button type="submit" class="text-sm text-red-400 hover:underline">Batalkan pesanan</button>
                    </form>
                @endif
            </div>
        @endif

        @if ($whatsappUrl)
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                class="inline-block rounded-md border border-green-500 px-4 py-2 text-sm text-green-300 hover:bg-green-500/10">
                Tanya pesanan lewat WhatsApp
            </a>
        @endif
    </div>
</div>
@endsection
