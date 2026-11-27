<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);

        $orders = Order::with('user')
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where('order_number', 'ilike', '%' . $term . '%');
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn ($query, $payment) => $query->where('payment_status', $payment))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.orders.index', compact('orders', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'user', 'handler']);

        return view('staff.orders.show', compact('order'));
    }

    public function approvePayment(Request $request, Order $order): RedirectResponse
    {
        if (! $this->isWaitingForReview($order)) {
            return back()->with('error', 'Pembayaran pesanan ini tidak sedang menunggu pemeriksaan.');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Pembayaran dikonfirmasi. Pesanan berstatus Dibayar.');
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

        return back()->with('success', 'Bukti bayar ditolak. Pelanggan bisa mengunggah ulang.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        // Hanya status tujuan yang diizinkan oleh alur (Order::NEXT_STATUSES) yang diterima.
        $data = $request->validate([
            'status' => ['required', Rule::in($order->nextStatuses())],
        ], [
            'status.required' => 'Pilih status tujuan.',
            'status.in' => 'Status tujuan tidak valid untuk pesanan ini.',
        ]);

        $order->update([
            'status' => $data['status'],
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        // Pembatalan oleh admin saja.
        abort_unless($request->user()->isAdmin(), 403);

        if (! $order->canBeCancelledByAdmin()) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        $order->load('items');
        $order->cancelAndRestoreStock($request->user()->id);

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    private function isWaitingForReview(Order $order): bool
    {
        return $order->status === Order::STATUS_PENDING_PAYMENT
            && $order->payment_status === Order::PAYMENT_PENDING_REVIEW;
    }
}
