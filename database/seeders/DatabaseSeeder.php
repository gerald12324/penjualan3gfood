<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Courier;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin 3G FOOD',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        foreach ([['Budi Santoso', '081234567801'], ['Citra Lestari', '081234567802'], ['Deni Pratama', '081234567803']] as [$name, $phone]) {
            Courier::firstOrCreate(['phone' => $phone], ['name' => $name, 'is_available' => true]);
        }
    }
}
