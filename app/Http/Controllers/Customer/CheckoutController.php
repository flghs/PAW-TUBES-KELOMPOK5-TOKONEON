<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Support\OrderAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    // Halaman ringkasan dan form alamat.
    public function create(Request $request, Product $product): View|RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $quantity = (int) $request->query('quantity', 1);

        if ($quantity < 1 || $quantity > 100) {
            $quantity = 1;
        }

        if ($product->stock < 1) {
            return redirect()
                ->route('catalog.show', $product->slug)
                ->with('error', 'Stok produk habis.');
        }

        if ($quantity > $product->stock) {
            return redirect()
                ->route('catalog.show', $product->slug)
                ->with('error', "Stok hanya tersisa {$product->stock}.");
        }

        return view('customer.checkout', compact('product', 'quantity'));
    }

    // Membuat pesanan. Stok belum dikurangi di sini: stok dikurangi saat staff
    // menyetujui bukti bayar (siapa yang disetujui lebih dulu, dia yang dapat stok).
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (strlen(Order::normalizePhone($value)) < 9) {
                        $fail('Nomor telepon tidak valid.');
                    }
                },
            ],
            'shipping_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'quantity.required' => 'Isi jumlah.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'quantity.max' => 'Jumlah maksimal 100.',
            'recipient_name.required' => 'Isi nama penerima.',
            'phone.required' => 'Isi nomor telepon penerima.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda + atau -.',
            'shipping_address.required' => 'Isi alamat lengkap.',
            'city.required' => 'Isi kota.',
            'province.required' => 'Isi provinsi.',
            'postal_code.required' => 'Isi kode pos.',
        ]);

        // Stok hanya dicek di sini, belum dikunci.
        if ($product->stock < $data['quantity']) {
            return redirect()
                ->route('catalog.show', $product->slug)
                ->with('error', 'Stok tidak cukup atau produk sudah tidak tersedia.');
        }

        // Pesanan dan isinya dibuat sebagai satu paket: kalau satu gagal, semuanya dibatalkan.
        $order = DB::transaction(function () use ($product, $data) {
            // Harga selalu diambil dari database, bukan dari form.
            $subtotal = $product->price * $data['quantity'];

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'type' => Order::TYPE_READY,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'payment_status' => Order::PAYMENT_UNPAID,
                'payment_method' => 'transfer',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Nama dan harga disalin (snapshot), jadi tidak berubah kalau produk diubah nanti.
            $order->items()->create([
                'product_id' => $product->id,
                'item_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $data['quantity'],
                'subtotal' => $subtotal,
            ]);

            return $order;
        });

        // Pembeli tidak punya akun: pesanan ini dicatat di sesi browsernya.
        OrderAccess::grant($request, $order);

        return redirect()
            ->route('orders.show', $order->order_number)
            ->with('success', 'Pesanan berhasil dibuat. Simpan nomor pesananmu, lalu lakukan pembayaran.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
