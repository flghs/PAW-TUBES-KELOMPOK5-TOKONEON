<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\OrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Halaman pesanan untuk pembeli tanpa akun.
class OrderController extends Controller
{
    // Bukti bayar disimpan di disk privat (tidak bisa dibuka lewat alamat publik).
    private const FILE_DISK = 'local';

    // Form lacak pesanan: nomor pesanan + nomor telepon.
    public function track(): View
    {
        return view('customer.track');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
        ], [
            'order_number.required' => 'Isi nomor pesanan.',
            'phone.required' => 'Isi nomor telepon yang dipakai saat memesan.',
        ]);

        $order = Order::where('order_number', strtoupper(trim($data['order_number'])))->first();

        $phone = Order::normalizePhone($data['phone']);

        // Pesan error sengaja sama untuk "nomor salah" dan "telepon salah".
        $matches = $order
            && $phone !== ''
            && hash_equals(Order::normalizePhone($order->phone), $phone);

        if (! $matches) {
            return back()
                ->withInput($request->only('order_number'))
                ->withErrors(['order_number' => 'Pesanan tidak ditemukan atau nomor telepon tidak cocok.']);
        }

        OrderAccess::grant($request, $order);

        return redirect()->route('orders.show', $order->order_number);
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        if (! OrderAccess::has($request, $order)) {
            return $this->askToVerify($order);
        }

        $order->load('items');

        $whatsappUrl = $this->whatsappUrl($order);

        return view('customer.orders.show', compact('order', 'whatsappUrl'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (! OrderAccess::has($request, $order)) {
            return $this->askToVerify($order);
        }

        // Pembeli hanya boleh membatalkan sebelum membayar.
        if (! $order->cancel(null, true)) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        if (! OrderAccess::has($request, $order)) {
            return $this->askToVerify($order);
        }

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

    // Browser ini belum terverifikasi untuk pesanan tersebut: minta nomor pesanan + telepon.
    private function askToVerify(Order $order): RedirectResponse
    {
        return redirect()
            ->route('orders.track')
            ->withInput(['order_number' => $order->order_number])
            ->with('error', 'Masukkan nomor telepon yang dipakai saat memesan untuk melihat pesanan ini.');
    }

    // Tombol chat ke nomor WhatsApp bisnis (null kalau nomornya belum diatur di .env).
    private function whatsappUrl(Order $order): ?string
    {
        $number = preg_replace('/\D+/', '', (string) config('shop.whatsapp_number'));

        if ($number === '') {
            return null;
        }

        $message = "Halo Neon Store, saya ingin menanyakan pesanan {$order->order_number}.";

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}
