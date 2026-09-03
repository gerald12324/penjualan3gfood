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

        dump($response->status(), $response->headers->get('Location', 'no-location'), session()->all());

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
                 'address' => 'Jl. Taman',
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
}
