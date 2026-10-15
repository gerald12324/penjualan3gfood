<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_logout_via_post_and_redirects_to_admin_login_with_success(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@3gfood.com',
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar dari Dashboard Admin.');
        $this->assertGuest('web');
    }

    public function test_admin_can_logout_via_get_without_405_error(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test2@3gfood.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest('web');
    }

    public function test_admin_logout_alias_works_for_post_and_get(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test3@3gfood.com',
        ]);

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect(route('login'));

        $this->actingAs($admin);
        $responseGet = $this->get('/logout');
        $responseGet->assertRedirect(route('login'));
    }

    public function test_admin_ajax_logout_returns_json_response(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_ajax@3gfood.com',
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect' => route('login'),
        ]);
        $this->assertGuest('web');
    }

    public function test_unauthenticated_user_accessing_logout_redirects_to_admin_login(): void
    {
        $response = $this->get('/logout');
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get('/admin/logout');
        $responseAdmin->assertRedirect(route('login'));
    }
}
