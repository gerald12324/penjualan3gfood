<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Courier;
use App\Models\MenuItem;
use App\Models\PengaturanToko;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama Default
        User::firstOrCreate(
            ['email' => 'admin@3gfood.com'],
            [
                'name' => 'Admin 3G FOOD',
                'password' => bcrypt('admin123'),
            ]
        );

        // 2. Pengaturan Toko
        PengaturanToko::firstOrCreate(
            ['id' => 1],
            [
                'status_toko' => 'buka',
                'jam_buka' => '08:00',
                'jam_tutup' => '22:00',
            ]
        );

        // 3. Data Menu Makanan Asli Beserta Foto
        $menus = [
            [
                'name' => 'Paket 2',
                'description' => 'Nasi Liwet Teri + Ayam Bakar/Goreng + Sambel + Lalapan',
                'category' => 'Paket Nasi Liwet',
                'price' => 14000,
                'image_url' => 'http://127.0.0.1:8000/storage/menu/puCGFvS8bKUMfGdLG0ZpthXOSWwGC0eY3sTFFODF.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Paket 3',
                'description' => 'Nasi Liwet Teri + Ayam Bakar/Goreng + Tempe Tepung + Sambel + Lalapan',
                'category' => 'Paket Nasi Liwet',
                'price' => 15000,
                'image_url' => 'http://127.0.0.1:8000/storage/menu/qUkoiFSr63UWCiZzHPeKjygzcDbKbShcdWx3QH6t.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Pecak Mujaer',
                'description' => 'Mujaer pecak+ lalapan',
                'category' => 'Lauk Utama',
                'price' => 15000,
                'image_url' => 'http://127.0.0.1:8000/storage/menu/uYvQR5tNNdIqwsmCO8CtiAwlKCAjY1riECssC0SU.jpg',
                'is_available' => true,
            ],
        ];

        foreach ($menus as $m) {
            MenuItem::updateOrCreate(['name' => $m['name']], $m);
        }

        // 4. Data Kurir
        foreach ([['Budi Santoso', '081234567801'], ['Citra Lestari', '081234567802'], ['Deni Pratama', '081234567803']] as [$name, $phone]) {
            Courier::firstOrCreate(['phone' => $phone], ['name' => $name, 'is_available' => true]);
        }
    }
}
