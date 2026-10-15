<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuFlowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_customer_site_uses_database_menu_ids_and_updates_after_admin_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/admin/menu', [
            'name' => 'Nasi Pecel Spesial',
            'description' => 'Menu baru dari admin',
            'category' => 'Lauk Utama',
            'price' => 25000,
            'is_available' => '1',
        ]);


        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menu_items', [
            'name' => 'Nasi Pecel Spesial',
            'price' => 25000,
            'category' => 'Lauk Utama',
            'is_available' => true,
        ]);

        $menu = MenuItem::where('name', 'Nasi Pecel Spesial')->firstOrFail();

        $this->get('/?screen=home')->assertSee('Nasi Pecel Spesial');
        $this->get('/cart/add/' . $menu->id)->assertRedirect('/?screen=home');

        $this->withSession(['cart' => [$menu->id => 1]])
             ->post('/checkout/buyer', [
                 'customer_name' => 'Budi',
                 'phone' => '081234567890',
                 'perumahan' => 'Graha Asri',
                 'blok' => 'A1',
             ])
             ->assertRedirect('/?screen=payment');

        $this->withSession(['cart' => [$menu->id => 1], 'buyer' => [
            'customer_name' => 'Budi',
            'phone' => '081234567890',
            'address' => 'Jl. Taman',
        ]])
             ->post('/checkout/payment', ['payment_method' => 'cash'])
             ->assertRedirect('/?screen=confirmation');

        $this->assertDatabaseHas('orders', ['customer_name' => 'Budi']);

        $this->patch('/admin/menu/' . $menu->id, [
            'name' => 'Nasi Pecel Updated',
            'description' => 'Updated desc',
            'category' => 'Lauk Utama',
            'price' => 28000,
            'is_available' => '1',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('menu_items', ['id' => $menu->id, 'name' => 'Nasi Pecel Updated', 'price' => 28000]);
        $this->get('/?screen=home')->assertSee('Nasi Pecel Updated');

        $this->delete('/admin/menu/' . $menu->id)->assertSessionHas('success');
        $this->assertDatabaseMissing('menu_items', ['id' => $menu->id]);
        $this->get('/?screen=home')->assertDontSee('Nasi Pecel Updated');
    }

    public function test_admin_can_toggle_menu_visibility_and_customer_view_filters_it(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Buat menu dengan status default visible
        $menu = MenuItem::create([
            'name' => 'Ayam Penyet Sambal Ijo',
            'category' => 'Lauk Utama',
            'price' => 18000,
            'description' => 'Ayam goreng sambal cabe ijo khas',
            'is_available' => true,
            'is_visible' => true,
        ]);

        // Verifikasi menu terlihat di etalase pembeli
        $this->get('/?screen=home')
            ->assertStatus(200)
            ->assertSee('Ayam Penyet Sambal Ijo');

        // Verifikasi menu terlihat di dashboard admin dengan badge 'Tampil'
        $this->get('/admin?section=menu')
            ->assertStatus(200)
            ->assertSee('Ayam Penyet Sambal Ijo')
            ->assertSee('Tampil');

        // 2. Admin mengubah visibilitas (Hide / Sembunyikan)
        $toggleRes = $this->patch('/admin/menu/' . $menu->id . '/toggle-visibility');
        $toggleRes->assertSessionHas('success');

        $menu->refresh();
        $this->assertFalse($menu->is_visible);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menu->id,
            'is_visible' => false,
        ]);

        // 3. Verifikasi menu TIDAK MUNCUL di etalase pembeli
        $this->flushSession();
        $this->get('/?screen=home')
            ->assertStatus(200)
            ->assertDontSee('Ayam Penyet Sambal Ijo');

        // Percobaan tambah ke keranjang untuk menu tersembunyi ditolak
        $this->get('/cart/add/' . $menu->id)
            ->assertRedirect('/?screen=home')
            ->assertSessionHas('error');

        // 4. Verifikasi menu TETAP ADA di tabel admin dengan indikator 'Tersembunyi' dan tombol 'Tampilkan'
        $this->get('/admin?section=menu')
            ->assertStatus(200)
            ->assertSee('Ayam Penyet Sambal Ijo')
            ->assertSee('Tersembunyi')
            ->assertSee('Tampilkan');

        // 5. Admin mengaktifkan kembali (Show / Tampilkan) via AJAX / JSON
        $jsonToggleRes = $this->patchJson('/admin/menu/' . $menu->id . '/toggle-visibility');
        $jsonToggleRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_visible', true);

        $menu->refresh();
        $this->assertTrue($menu->is_visible);

        // 6. Menu muncul kembali di etalase pembeli
        $this->get('/?screen=home')
            ->assertStatus(200)
            ->assertSee('Ayam Penyet Sambal Ijo');
    }
}

