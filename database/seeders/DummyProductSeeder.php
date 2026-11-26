<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

// Produk contoh hanya untuk menguji tampilan katalog. Dihapus setelah selesai dites.
class DummyProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id');

        if ($categories->isEmpty()) {
            $this->command->warn('Belum ada kategori. Jalankan dulu: php artisan db:seed');

            return;
        }

        for ($i = 1; $i <= 14; $i++) {
            Product::firstOrCreate(
                ['slug' => 'produk-dummy-' . $i],
                [
                    'category_id' => $categories[($i - 1) % $categories->count()],
                    'name' => 'Produk Dummy ' . $i,
                    'description' => 'Produk contoh untuk menguji tampilan katalog.',
                    'price' => 100000 + $i * 10000,
                    'stock' => $i % 4,
                    'is_active' => $i !== 5,
                ]
            );
        }
    }
}
