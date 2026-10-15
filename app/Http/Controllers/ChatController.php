<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatMessage;
use App\Models\Customer;

class ChatController extends Controller
{
    /**
     * Helper to get customer conversation identifiers
     */
    protected function getCustomerIdentifier(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $sessionId = $request->hasSession() ? $request->session()->getId() : 'sess_' . ($customer?->id ?? 'guest');

        return [
            'customer' => $customer,
            'customer_id' => $customer?->id,
            'session_id' => $sessionId,
        ];
    }

    /**
     * Ambil riwayat chat atau polling pesan baru untuk pembeli
     */
    public function getCustomerMessages(Request $request)
    {
        $ident = $this->getCustomerIdentifier($request);
        $lastId = (int) $request->query('last_id', 0);

        $query = ChatMessage::query();
        if ($ident['customer_id']) {
            ChatMessage::where('session_id', $ident['session_id'])
                ->whereNull('customer_id')
                ->update([
                    'customer_id' => $ident['customer_id'],
                    'sender_name' => $ident['customer']->name,
                ]);

            $query->where('customer_id', $ident['customer_id']);
        } else {
            $query->where('session_id', $ident['session_id']);
        }

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('id', 'asc')->get();

        // Tandai pesan dari admin sebagai terbaca
        if ($messages->isNotEmpty()) {
            $adminMsgIds = $messages->where('sender_type', 'admin')->pluck('id');
            if ($adminMsgIds->isNotEmpty()) {
                ChatMessage::whereIn('id', $adminMsgIds)->update(['is_read' => true]);
            }
        }

        $formatted = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_name,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i'),
                'is_read' => $msg->is_read,
            ];
        });

        // Hitung unread count jika initial load
        $unreadCount = 0;
        if ($lastId === 0) {
            $unreadQuery = ChatMessage::where('sender_type', 'admin')->where('is_read', false);
            if ($ident['customer_id']) {
                $unreadQuery->where('customer_id', $ident['customer_id']);
            } else {
                $unreadQuery->where('session_id', $ident['session_id']);
            }
            $unreadCount = $unreadQuery->count();
        }

        return response()->json([
            'success' => true,
            'messages' => $formatted,
            'unread_count' => $unreadCount,
            'customer_name' => $ident['customer']?->name ?? 'Tamu',
        ]);
    }

    /**
     * Kirim pesan dari pembeli ke admin
     */
    public function sendCustomerMessage(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'guest_name' => ['nullable', 'string', 'max:60'],
        ]);

        $ident = $this->getCustomerIdentifier($request);
        $customer = $ident['customer'];

        $senderName = $customer
            ? $customer->name
            : ($request->filled('guest_name') ? $request->input('guest_name') : 'Tamu #' . substr($ident['session_id'], 0, 4));

        $chat = ChatMessage::create([
            'customer_id' => $ident['customer_id'],
            'session_id' => $ident['session_id'],
            'sender_type' => 'customer',
            'sender_name' => $senderName,
            'message' => trim($request->input('message')),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chat->id,
                'sender_type' => $chat->sender_type,
                'sender_name' => $chat->sender_name,
                'message' => $chat->message,
                'time' => $chat->created_at->format('H:i'),
                'is_read' => false,
            ],
        ]);
    }

    /**
     * ADMIN: Ambil daftar percakapan aktif
     */
    public function getAdminConversations()
    {
        // Ambil semua pesan terbaru per sesi/customer
        $allMessages = ChatMessage::with('customer')
            ->orderBy('id', 'desc')
            ->limit(200)
            ->get();

        $grouped = [];

        foreach ($allMessages as $msg) {
            $key = $msg->customer_id ? 'c_' . $msg->customer_id : 's_' . $msg->session_id;

            if (!isset($grouped[$key])) {
                $customer = $msg->customer;
                $name = $customer ? $customer->name : $msg->sender_name;
                $phone = $customer?->phone ?? '-';
                $avatar = $customer?->avatar_url ?? null;

                $grouped[$key] = [
                    'key' => $key,
                    'customer_id' => $msg->customer_id,
                    'session_id' => $msg->session_id,
                    'name' => $name,
                    'phone' => $phone,
                    'avatar' => $avatar,
                    'is_customer' => (bool) $customer,
                    'last_message' => $msg->message,
                    'last_time' => $msg->created_at->format('d/m H:i'),
                    'last_timestamp' => $msg->created_at->timestamp,
                    'unread_count' => 0,
                ];
            }
        }

        // Hitung unread count per conversation
        $unreads = ChatMessage::where('sender_type', 'customer')
            ->where('is_read', false)
            ->get();

        foreach ($unreads as $unread) {
            $key = $unread->customer_id ? 'c_' . $unread->customer_id : 's_' . $unread->session_id;
            if (isset($grouped[$key])) {
                $grouped[$key]['unread_count']++;
            }
        }

        // Urutkan berdasarkan pesan terakhir
        $conversations = array_values($grouped);
        usort($conversations, fn($a, $b) => $b['last_timestamp'] <=> $a['last_timestamp']);

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
            'total_unread' => $unreads->count(),
        ]);
    }

    /**
     * ADMIN: Ambil isi percakapan dengan pelanggan tertentu
     */
    public function getAdminThread(Request $request, string $conversationKey)
    {
        $lastId = (int) $request->query('last_id', 0);

        $query = ChatMessage::query();
        if (str_starts_with($conversationKey, 'c_')) {
            $customerId = (int) substr($conversationKey, 2);
            $query->where('customer_id', $customerId);
        } else {
            $sessionId = substr($conversationKey, 2);
            $query->where('session_id', $sessionId);
        }

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('id', 'asc')->get();

        // Tandai pesan customer sebagai terbaca
        if ($messages->isNotEmpty()) {
            $custMsgIds = $messages->where('sender_type', 'customer')->pluck('id');
            if ($custMsgIds->isNotEmpty()) {
                ChatMessage::whereIn('id', $custMsgIds)->update(['is_read' => true]);
            }
        }

        $formatted = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_name,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i'),
                'is_read' => $msg->is_read,
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formatted,
        ]);
    }

    /**
     * ADMIN: Kirim balasan ke pelanggan
     */
    public function sendAdminReply(Request $request)
    {
        $request->validate([
            'conversation_key' => ['required', 'string'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $key = $request->input('conversation_key');
        $customerId = null;
        $sessionId = null;

        if (str_starts_with($key, 'c_')) {
            $customerId = (int) substr($key, 2);
            // Cari sesi terakhir jika ada
            $lastMsg = ChatMessage::where('customer_id', $customerId)->latest()->first();
            $sessionId = $lastMsg?->session_id ?? 'session_' . $customerId;
        } else {
            $sessionId = substr($key, 2);
        }

        $chat = ChatMessage::create([
            'customer_id' => $customerId,
            'session_id' => $sessionId,
            'sender_type' => 'admin',
            'sender_name' => 'Admin 3GFood',
            'message' => trim($request->input('message')),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chat->id,
                'sender_type' => $chat->sender_type,
                'sender_name' => $chat->sender_name,
                'message' => $chat->message,
                'time' => $chat->created_at->format('H:i'),
                'is_read' => false,
            ],
        ]);
    }
}
