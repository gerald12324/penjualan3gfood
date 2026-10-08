<?php

namespace Tests\Feature;

use App\Models\Courier;
use App\Models\CourierPayout;
use App\Models\MenuItem;
use App\Models\PengaturanToko;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAndVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_status_banner_is_shown_when_shop_is_closed(): void
    {
        PengaturanToko::create([
            'status_toko' => 'tutup',
            'jam_buka' => '08:00',
            'jam_tutup' => '21:00',
        ]);

        $response = $this->get('/?screen=home');

        $response->assertSee('Toko sedang tutup');
    }

    public function test_voucher_requires_minimum_purchase_before_applying_discount(): void
    {
        MenuItem::create([
            'name' => 'Nasi Liwet',
            'category' => 'Paket Nasi Liwet',
            'price' => 20000,
            'description' => 'Deskripsi test',
            'is_available' => true,
        ]);

        Voucher::create([
            'kode_voucher' => 'SAVE50',
            'jenis_potongan' => 'nominal',
            'nilai_potongan' => 5000,
            'min_belanja' => 50000,
            'tanggal_berakhir' => now()->addDays(7)->toDateString(),
            'kuota' => 10,
            'status' => 'aktif',
        ]);

        $this->withSession(['cart' => [1 => 1]])
            ->postJson('/voucher/check', ['voucher_code' => 'SAVE50', 'subtotal' => 20000])
            ->assertStatus(422)
            ->assertJsonPath('valid', false);
    }

    public function test_reports_page_loads_with_existing_payout_schema(): void
    {
        $this->actingAs($this->createUser());

        $courier = Courier::create([
            'name' => 'Kurir Test',
            'phone' => '08123456789',
            'is_available' => true,
        ]);

        CourierPayout::create([
            'courier_id' => $courier->id,
            'amount' => 150000,
            'period' => 'September 2026',
            'status' => 'pending',
            'notes' => 'Test payout',
        ]);

        $response = $this->get('/admin?section=reports');

        $response->assertStatus(200)
            ->assertSee('Laporan Penjualan');
    }

    private function createUser(): \App\Models\User
    {
        return \App\Models\User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
