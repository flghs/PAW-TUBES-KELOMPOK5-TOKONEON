<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->saveUser('admin@example.test', 'Admin', User::ROLE_ADMIN);
        $this->saveUser('staff@example.test', 'Staff Demo', User::ROLE_STAFF);
        $this->saveUser('customer@example.test', 'Pelanggan Demo', User::ROLE_CUSTOMER);

        $categories = [
            ['name' => 'Neon Teks', 'slug' => 'neon-teks', 'description' => 'Neon sign berbentuk tulisan.'],
            ['name' => 'Neon Logo', 'slug' => 'neon-logo', 'description' => 'Neon sign berbentuk logo atau gambar.'],
            ['name' => 'Neon Dekorasi', 'slug' => 'neon-dekorasi', 'description' => 'Neon sign untuk dekorasi ruangan.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }

    private function saveUser(string $email, string $name, string $role): void
    {
        // role dan is_active tidak ada di $fillable, jadi diisi dengan forceFill di kode server ini.
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $name,
            'password' => 'password123',
            'role' => $role,
            'is_active' => true,
        ])->save();
    }
}
