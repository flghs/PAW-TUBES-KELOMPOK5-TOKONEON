<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Http\Request;

// Pembeli tidak punya akun. Setelah membuat pesanan, atau setelah mengisi nomor pesanan
// dan nomor telepon yang cocok, pesanan itu dicatat di sesi browser pembeli.
class OrderAccess
{
    private const KEY = 'order_access';

    public static function grant(Request $request, Order $order): void
    {
        $ids = $request->session()->get(self::KEY, []);
        $ids[] = $order->id;

        // Simpan paling banyak 20 pesanan terakhir per browser.
        $request->session()->put(self::KEY, array_slice(array_values(array_unique($ids)), -20));
    }

    public static function has(Request $request, Order $order): bool
    {
        $ids = array_map('intval', $request->session()->get(self::KEY, []));

        return in_array($order->id, $ids, true);
    }
}
