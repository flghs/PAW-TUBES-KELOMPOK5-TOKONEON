<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // true = stok produk sudah dikurangi untuk pesanan ini.
            $table->boolean('stock_deducted')->default(false);
            // true = pembayaran lunas tapi stok tidak cukup, staff perlu menghubungi pembeli.
            $table->boolean('stock_short')->default(false);
        });

        // Pesanan lama mengurangi stok saat dibuat, jadi tandai sudah terpotong
        // supaya kalau dibatalkan stoknya tetap dikembalikan dengan benar.
        DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->update(['stock_deducted' => true]);

        // Akun pelanggan tidak dipakai lagi.
        DB::table('users')->where('role', 'customer')->delete();
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['stock_deducted', 'stock_short']);
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
        });
    }
};
