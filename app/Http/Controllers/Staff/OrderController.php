<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::in(array_keys(Order::STATUS_LABELS))],
            'payment_status' => ['nullable', Rule::in(array_keys(Order::PAYMENT_LABELS))],
            'attention' => ['nullable', 'boolean'],
        ]);

        $orders = Order::query()
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where(function ($inner) use ($term) {
                    $like = '%' . $term . '%';

                    $inner->where('order_number', 'ilike', $like)
                        ->orWhere('recipient_name', 'ilike', $like)
                        ->orWhere('phone', 'ilike', $like);
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn ($query, $payment) => $query->where('payment_status', $payment))
            // "Perlu dihubungi" = lunas tapi stok kurang.
            ->when($filters['attention'] ?? null, fn ($query) => $query->where('stock_short', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.orders.index', compact('orders', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'handler']);

        return view('staff.orders.show', compact('order'));
    }

    // Menyetujui pembayaran sekaligus mengambil stok. Pesanan yang disetujui lebih dulu
    // mendapat stok; kalau stok sudah habis, pesanan tetap lunas tapi diberi penanda
    // "stok kurang" supaya staff menghubungi pembeli (uang kembali atau menunggu produksi).
    public function approvePayment(Request $request, Order $order): RedirectResponse
    {
        $result = DB::transaction(function () use ($request, $order) {
            // Kunci pesanan supaya tidak disetujui dua kali bersamaan.
            $locked = Order::whereKey($order->id)->lockForUpdate()->with('items')->firstOrFail();

            if (! $this->isWaitingForReview($locked)) {
                return null;
            }

            // Pesanan lama (dibuat sebelum aturan ini) sudah memotong stok saat dibuat,
            // jadi jangan dipotong dua kali.
            $deduct = $locked->stock_deducted;
            $short = false;

            if (! $deduct) {
                // Jumlah yang dibutuhkan per produk, diurutkan supaya urutan penguncian selalu sama.
                $needs = $locked->items
                    ->whereNotNull('product_id')
                    ->groupBy('product_id')
                    ->map(fn ($items) => $items->sum('quantity'))
                    ->sortKeys();

                // Kunci baris produk supaya dua persetujuan tidak mengambil stok yang sama.
                $products = Product::whereIn('id', $needs->keys()->all())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $enough = $needs->every(
                    fn ($quantity, $productId) => isset($products[$productId]) && $products[$productId]->stock >= $quantity
                );

                $deduct = $needs->isNotEmpty() && $enough;
                $short = $needs->isNotEmpty() && ! $enough;

                if ($deduct) {
                    foreach ($needs as $productId => $quantity) {
                        $products[$productId]->decrement('stock', $quantity);
                    }
                }
            }

            $locked->update([
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_PAID,
                'paid_at' => now(),
                'handled_by' => $request->user()->id,
                'stock_deducted' => $deduct,
                'stock_short' => $short,
            ]);

            return $short ? 'short' : 'ok';
        });

        if ($result === null) {
            return back()->with('error', 'Pembayaran pesanan ini tidak sedang menunggu pemeriksaan.');
        }

        if ($result === 'short') {
            return back()->with('success', 'Pembayaran dikonfirmasi, tetapi stok tidak cukup. Hubungi pembeli: uang dikembalikan atau menunggu barang dibuat.');
        }

        return back()->with('success', 'Pembayaran dikonfirmasi dan stok dikurangi. Pesanan berstatus Dibayar.');
    }

    public function rejectPayment(Request $request, Order $order): RedirectResponse
    {
        if (! $this->isWaitingForReview($order)) {
            return back()->with('error', 'Pembayaran pesanan ini tidak sedang menunggu pemeriksaan.');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_REJECTED,
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Bukti bayar ditolak. Pembeli bisa mengunggah ulang.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        // Hanya status tujuan yang diizinkan oleh alur (Order::nextStatuses) yang diterima.
        $data = $request->validate([
            'status' => ['required', Rule::in($order->nextStatuses())],
        ], [
            'status.required' => 'Pilih status tujuan.',
            'status.in' => 'Status tujuan tidak valid untuk pesanan ini.',
        ]);

        // Pembeli yang stoknya kurang dan memilih menunggu sudah ditangani,
        // jadi penanda "stok kurang" dihapus begitu status berubah.
        $order->update([
            'status' => $data['status'],
            'stock_short' => false,
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    // Staff dan admin sama-sama boleh membatalkan.
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        $restoresStock = $order->stock_deducted;

        if (! $order->cancel($request->user()->id)) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        return back()->with(
            'success',
            $restoresStock ? 'Pesanan dibatalkan dan stok dikembalikan.' : 'Pesanan dibatalkan.'
        );
    }

    private function isWaitingForReview(Order $order): bool
    {
        return $order->status === Order::STATUS_PENDING_PAYMENT
            && $order->payment_status === Order::PAYMENT_PENDING_REVIEW;
    }
}
