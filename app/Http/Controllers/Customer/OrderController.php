<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    // Bukti bayar disimpan di disk privat (tidak bisa dibuka lewat alamat publik).
    private const FILE_DISK = 'local';

    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureOwner($request, $order);

        $order->load('items');

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->ensureOwner($request, $order);

        if (! $order->isAwaitingPayment()) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            // Stok yang tadi dikurangi dikembalikan.
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        $this->ensureOwner($request, $order);

        if (! $order->isAwaitingPayment()) {
            return back()->with('error', 'Bukti bayar tidak bisa diunggah untuk pesanan ini.');
        }

        $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'proof.required' => 'Pilih foto bukti bayar.',
            'proof.image' => 'File harus berupa gambar.',
            'proof.mimes' => 'Gambar harus berformat jpg, png, atau webp.',
            'proof.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $oldProof = $order->payment_proof_path;
        $path = $request->file('proof')->store('payment-proofs', self::FILE_DISK);

        $order->update([
            'payment_proof_path' => $path,
            'payment_status' => Order::PAYMENT_PENDING_REVIEW,
        ]);

        // Bukti lama (kalau ada) dihapus setelah yang baru tersimpan.
        if ($oldProof) {
            Storage::disk(self::FILE_DISK)->delete($oldProof);
        }

        return back()->with('success', 'Bukti bayar terkirim dan akan diperiksa.');
    }

    // Pelanggan hanya boleh membuka pesanan miliknya sendiri.
    private function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
