<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Normalize phone number to international format (e.g. 628...)
     */
    public static function normalizePhone($phone)
    {
        $phone = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '628' . substr($phone, 2);
        }
        return $phone;
    }

    /**
     * Send WhatsApp notification for an order
     */
    public static function sendMessage(Order $order, $message)
    {
        $phone = self::normalizePhone($order->phone);
        
        $token = config('services.whatsapp.access_token');
        $phoneId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.api_version', 'v17.0');

        if (!$token || !$phoneId) {
            return self::createLog($order->id, $phone, $message, 'failed', json_encode(['error' => 'WhatsApp credentials not configured']));
        }

        $url = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        try {
            $response = Http::withToken($token)
                ->post($url, $payload);

            $status = $response->successful() ? 'sent' : 'failed';
            $apiResponse = $response->body();
            
            return self::createLog($order->id, $phone, $message, $status, $apiResponse, $status === 'sent' ? now() : null);
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return self::createLog($order->id, $phone, $message, 'failed', json_encode(['error' => $e->getMessage()]));
        }
    }

    private static function createLog($orderId, $phone, $message, $status, $apiResponse, $sentAt = null)
    {
        return WhatsappLog::create([
            'order_id' => $orderId,
            'phone_number' => $phone,
            'message' => $message,
            'status' => $status,
            'api_response' => $apiResponse,
            'sent_at' => $sentAt,
        ]);
    }
}
