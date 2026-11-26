<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Cart $cart): View
    {
        if ($cart->refresh()) {
            session()->now('error', 'Isi keranjang disesuaikan karena stok atau ketersediaan produk berubah.');
        }

        $items = $cart->items();

        return view('cart.index', [
            'items' => $items,
            'total' => (int) $items->sum('subtotal'),
        ]);
    }

    public function store(Request $request, Cart $cart, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'quantity.required' => 'Isi jumlah.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'quantity.max' => 'Jumlah maksimal 100 per produk.',
        ]);

        if ($product->stock < 1) {
            return back()->with('error', 'Stok produk habis.');
        }

        $inCart = $cart->quantityOf($product->id);
        $newQuantity = $inCart + $data['quantity'];

        if ($newQuantity > $product->stock) {
            return back()->with(
                'error',
                "Stok hanya tersisa {$product->stock}, dan di keranjangmu sudah ada {$inCart}."
            );
        }

        $cart->set($product->id, $newQuantity);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Cart $cart, Product $product): RedirectResponse
    {
        abort_unless($cart->quantityOf($product->id) > 0, 404);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'quantity.required' => 'Isi jumlah.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'quantity.max' => 'Jumlah maksimal 100 per produk.',
        ]);

        if ($data['quantity'] > $product->stock) {
            return back()->with('error', "Stok hanya tersisa {$product->stock}.");
        }

        $cart->set($product->id, $data['quantity']);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Cart $cart, Product $product): RedirectResponse
    {
        $cart->remove($product->id);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
