<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_send_message_and_retrieve_chat_history(): void
    {
        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'phone' => '081234567890',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($customer, 'customer');

        // Customer sends message
        $response = $this->postJson('/chat/send', [
            'message' => 'Halo min, nasi liwet masih ada?',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.message', 'Halo min, nasi liwet masih ada?')
            ->assertJsonPath('message.sender_name', 'Budi Santoso');

        $this->assertDatabaseHas('chat_messages', [
            'customer_id' => $customer->id,
            'message' => 'Halo min, nasi liwet masih ada?',
            'sender_type' => 'customer',
        ]);

        // Customer retrieves messages
        $getRes = $this->getJson('/chat/messages');
        $getRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'messages');
    }

    public function test_guest_can_send_message_with_guest_name(): void
    {
        $response = $this->postJson('/chat/send', [
            'message' => 'Halo apakah ada promo hari ini?',
            'guest_name' => 'Siti Aminah',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.sender_name', 'Siti Aminah');

        $this->assertDatabaseHas('chat_messages', [
            'sender_name' => 'Siti Aminah',
            'message' => 'Halo apakah ada promo hari ini?',
            'sender_type' => 'customer',
        ]);
    }

    public function test_admin_can_view_conversations_and_send_reply(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $customer = Customer::create([
            'name' => 'Rian Hidayat',
            'email' => 'rian@test.com',
            'phone' => '08987654321',
            'password' => bcrypt('password123'),
        ]);

        // Customer sends message
        ChatMessage::create([
            'customer_id' => $customer->id,
            'session_id' => 'session_123',
            'sender_type' => 'customer',
            'sender_name' => 'Rian Hidayat',
            'message' => 'Apakah bisa request ayam bagian paha?',
            'is_read' => false,
        ]);

        $this->actingAs($admin);

        // Admin checks conversations list
        $convRes = $this->getJson('/admin/chat/conversations');
        $convRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('total_unread', 1);

        // Admin views thread
        $threadRes = $this->getJson('/admin/chat/thread/c_' . $customer->id);
        $threadRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'messages');

        // Verify message marked as read
        $this->assertDatabaseHas('chat_messages', [
            'customer_id' => $customer->id,
            'is_read' => true,
        ]);

        // Admin sends reply
        $replyRes = $this->postJson('/admin/chat/reply', [
            'conversation_key' => 'c_' . $customer->id,
            'message' => 'Bisa kak, kami siapkan paha atas ya!',
        ]);

        $replyRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.sender_type', 'admin');

        $this->assertDatabaseHas('chat_messages', [
            'customer_id' => $customer->id,
            'sender_type' => 'admin',
            'message' => 'Bisa kak, kami siapkan paha atas ya!',
        ]);
    }
}
