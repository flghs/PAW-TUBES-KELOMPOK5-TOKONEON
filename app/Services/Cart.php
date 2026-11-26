<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    private const SESSION_KEY = 'cart';

    /**
     * Isi mentah keranjang di session: [id produk => jumlah].
     *
     * @return array<int, int>
     */
    public function raw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    public function quantityOf(int $productId): int
    {
        return $this->raw()[$productId] ?? 0;
    }

    // Jumlah kurang dari 1 berarti item dihapus dari keranjang.
    public function set(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $this->set($productId, 0);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Isi keranjang lengkap dengan data produk terbaru dari database.
     * Harga selalu diambil dari database, bukan dari session.
     *
     * @return Collection<int, array{product: Product, quantity: int, subtotal: int}>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if ($cart === []) {
            return collect();
        }

        $products = Product::active()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->filter(fn (int $quantity, int $id) => $products->has($id))
            ->map(function (int $quantity, int $id) use ($products) {
                $product = $products[$id];

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })
            ->values();
    }

    public function total(): int
    {
        return (int) $this->items()->sum('subtotal');
    }

    /**
     * Menyesuaikan keranjang dengan keadaan database: produk yang dinonaktifkan,
     * dihapus, atau stoknya habis dibuang, dan jumlah dibatasi sesuai stok.
     * Mengembalikan true kalau ada yang diubah.
     */
    public function refresh(): bool
    {
        $cart = $this->raw();

        if ($cart === []) {
            return false;
        }

        $products = Product::active()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $fresh = [];

        foreach ($cart as $id => $quantity) {
            $product = $products->get($id);

            if (! $product || $product->stock < 1) {
                continue;
            }

            $fresh[$id] = min($quantity, $product->stock);
        }

        $changed = $fresh !== $cart;

        if ($changed) {
            session([self::SESSION_KEY => $fresh]);
        }

        return $changed;
    }
}
