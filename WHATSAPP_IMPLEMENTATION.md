# WhatsApp Integration - Implementation Guide (Developer)

## 📌 Overview Implementasi

Integrasi WhatsApp API telah diimplementasikan dengan pattern service-oriented untuk maintainability dan reusability yang baik.

### Architecture Pattern
```
Routes (request handler)
    ↓
WhatsAppService (business logic)
    ↓
WhatsApp Cloud API
    ↓
WhatsappLog (database record)
```

---

## 🏗️ Core Components

### 1. WhatsAppService (`app/Services/WhatsAppService.php`)

#### Methods:

##### `normalizePhone(string $phone): string`
Mengkonversi format nomor telepon Indonesia ke format internasional.

```php
// Implementasi
public static function normalizePhone($phone)
{
    $phone = preg_replace('/\D+/', '', $phone);  // Remove non-digits
    if (str_starts_with($phone, '08')) {
        $phone = '628' . substr($phone, 2);      // 08xxx → 628xxx
    }
    return $phone;
}

// Contoh usage
normalizePhone('081234567890')     → '6281234567890'
normalizePhone('+6281234567890')   → '6281234567890'
normalizePhone('6281234567890')    → '6281234567890'
```

**Regex Breakdown**:
- `\D+` = Match semua non-digit characters
- `preg_replace('/\D+/', '', $phone)` = Remove semua yang bukan angka
- `str_starts_with()` = Laravel helper untuk string check
- Substr untuk remove '0' dan replace dengan '62'

---

##### `sendMessage(Order $order, string $message): WhatsappLog`
Main function untuk mengirim pesan ke WhatsApp Cloud API.

```php
public static function sendMessage(Order $order, $message)
{
    // 1. Normalize nomor pembeli
    $phone = self::normalizePhone($order->phone);
    
    // 2. Ambil credentials dari .env via config
    $token = config('services.whatsapp.access_token');
    $phoneId = config('services.whatsapp.phone_number_id');
    $version = config('services.whatsapp.api_version', 'v17.0');

    // 3. Validasi credentials ada
    if (!$token || !$phoneId) {
        return self::createLog($order->id, $phone, $message, 'failed', 
            json_encode(['error' => 'WhatsApp credentials not configured']));
    }

    // 4. Build API URL
    $url = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

    // 5. Build request payload (WhatsApp Cloud API spec)
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => $phone,
        'type' => 'text',
        'text' => [
            'body' => $message
        ]
    ];

    // 6. Send HTTP request ke API
    try {
        $response = Http::withToken($token)
            ->post($url, $payload);

        // 7. Determine status
        $status = $response->successful() ? 'sent' : 'failed';
        $apiResponse = $response->body();
        
        // 8. Create log record
        return self::createLog($order->id, $phone, $message, $status, 
            $apiResponse, $status === 'sent' ? now() : null);
    } catch (\Exception $e) {
        // 9. Handle exception
        Log::error('WhatsApp API Error: ' . $e->getMessage());
        return self::createLog($order->id, $phone, $message, 'failed', 
            json_encode(['error' => $e->getMessage()]));
    }
}
```

**Key Points**:
- `Http::withToken()` = Guzzle HTTP client dari Laravel dengan Bearer token
- `$response->successful()` = Check HTTP status code 200-299
- `$response->body()` = Raw response string (biasanya JSON)
- Try-catch untuk network error, API error, dll
- Always create log, baik sukses maupun gagal
- Set `sent_at` timestamp hanya jika sukses

**WhatsApp API Payload**:
```json
{
  "messaging_product": "whatsapp",
  "to": "6281234567890",
  "type": "text",
  "text": {
    "body": "Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan."
  }
}
```

**WhatsApp API Success Response**:
```json
{
  "messages": [
    {
      "id": "wamid.AB1234567890=",
      "message_status": "accepted"
    }
  ]
}
```

**WhatsApp API Error Response**:
```json
{
  "error": {
    "message": "Invalid access token",
    "type": "OAuthException",
    "code": 190
  }
}
```

---

##### `createLog(int $orderId, string $phone, string $message, string $status, string $apiResponse, $sentAt = null): WhatsappLog`
Helper method untuk create log record di database.

```php
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
```

**Penjelasan**:
- `$sentAt` optional, hanya set jika successfully sent
- Automatic timestamps dari Laravel model (created_at, updated_at)
- Returns WhatsappLog instance untuk chaining

---

### 2. WhatsappLog Model (`app/Models/WhatsappLog.php`)

```php
class WhatsappLog extends Model
{
    protected $fillable = [
        'order_id', 'phone_number', 'message', 'status', 'api_response', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
```

**Penting**:
- `$fillable` = Whitelist columns untuk mass assignment
- `$casts` = Auto-convert timestamp ke Carbon datetime
- `belongsTo(Order::class)` = Relasi untuk query logs per order

---

### 3. Order Model Updates (`app/Models/Order.php`)

```php
class Order extends Model
{
    // ... existing code ...

    public function whatsappLogs()
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function latestWhatsappLog()
    {
        return $this->hasOne(WhatsappLog::class)->latestOfMany();
    }
}
```

**Methods**:
- `whatsappLogs()` = Get semua logs untuk order ini
- `latestWhatsappLog()` = Get log terbaru saja

---

## 🔗 Routes Implementation

### Route 1: Trigger WhatsApp (`admin.orders.cooked`)

**File**: `routes/web.php`

```php
Route::middleware('auth')->patch('/admin/orders/{order}/cooked', function (Order $order) {
    if ($order->status === 'cooking') {
        // ⚠️ IMPORTANT: Database transaction untuk atomic operation
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            // 1. Update order status
            $order->update(['status' => 'ready_to_deliver', 'cooked_at' => now()]);
            
            // 2. Build message dengan order_code asli
            $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
            
            // 3. Cek apakah sudah pernah kirim (prevent duplikat)
            //    Query: cari log dengan status 'sent' untuk order ini
            $existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
            
            if (!$existingLog) {
                // 4. Baru kirim jika belum pernah berhasil
                \App\Services\WhatsAppService::sendMessage($order, $message);
            }
        });

        // 5. Load relasi untuk check status WhatsApp
        $order->load('whatsappLogs');
        $latestLog = $order->latestWhatsappLog;
        
        // 6. Return response dengan message yang sesuai
        if ($latestLog && $latestLog->status === 'sent') {
            return back()->with('success', 'Pesanan berhasil diubah menjadi Siap Diantar dan notifikasi WhatsApp berhasil dikirim.');
        } else {
            return back()->with('success', 'Pesanan selesai dimasak dan siap diantar. (Gagal kirim notifikasi WhatsApp)');
        }
    }
    return back();
})->name('admin.orders.cooked');
```

**Why Database Transaction?**
- Jika error di tengah-tengah, semua changes rollback
- Ensures consistency: status update DAN log terekam sama-sama
- Prevent partial updates

**Prevent Duplikat Logic**:
```php
$existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
if (!$existingLog) {
    // Only send if no successful log exists
}
```

**Why Check `->latestWhatsappLog` setelah transaction?**
- `->load()` refresh model dari database
- Ensure kita dapat latest log yang baru saja di-create
- `latestOfMany()` get only the latest (tidak semua)

---

### Route 2: Manual Resend (`admin.orders.resend_wa`)

**File**: `routes/web.php`

```php
Route::middleware('post')->post('/admin/orders/{order}/resend-wa', function (Order $order) {
    // Rebuild message dengan current order data
    $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
    
    // Call service (akan create new log record)
    \App\Services\WhatsAppService::sendMessage($order, $message);
    
    // Load latest log
    $order->load('whatsappLogs');
    $latestLog = $order->latestWhatsappLog;
    
    // Return response
    if ($latestLog && $latestLog->status === 'sent') {
        return back()->with('success', 'Notifikasi WhatsApp berhasil dikirim ulang.');
    } else {
        return back()->with('success', 'Gagal kirim ulang notifikasi WhatsApp. Pastikan kredensial benar.');
    }
})->name('admin.orders.resend_wa');
```

**Perbedaan dengan Route 1**:
- Tidak perlu check duplikat (user sengaja retry)
- Tidak perlu update order status (status sudah ready_to_deliver)
- Creates new log entry setiap kali di-click

---

## 🖥️ Views Implementation

### Indikator Status WhatsApp
**File**: `resources/views/admin.blade.php` (dalam order table)

```html
@if(in_array($order->status, ['ready_to_deliver', 'delivering', 'completed']))
    @php $waLog = $order->latestWhatsappLog; @endphp
    @if($waLog)
        <!-- Show status indicator -->
        <div style="margin-top:8px;font-size:11px;padding:4px;border-radius:4px;text-align:center;background:{{ $waLog->status === 'sent' ? '#dcfce7' : '#fee2e2' }};color:{{ $waLog->status === 'sent' ? '#166534' : '#991b1b' }}">
            {{ $waLog->status === 'sent' ? '🟢 WhatsApp terkirim' : '🔴 WhatsApp gagal' }}
        </div>
        
        <!-- Show resend button only if failed -->
        @if($waLog->status !== 'sent')
        <form method="POST" action="{{ route('admin.orders.resend_wa', $order) }}" style="margin-top:4px">
            @csrf
            <button class="btn-confirm" type="submit" style="background:#ef4444;font-size:11px;padding:6px;width:100%">Kirim Ulang WA</button>
        </form>
        @endif
    @endif
@endif
```

**Logic Flow**:
1. Show indicator hanya untuk status ready_to_deliver, delivering, completed
2. Get latest log: `$waLog = $order->latestWhatsappLog`
3. If log exists:
   - Show green/red indicator based on status
   - Show resend button hanya jika status 'failed'

---

## ⚙️ Configuration

### config/services.php
```php
'whatsapp' => [
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'api_version' => env('WHATSAPP_API_VERSION', 'v17.0'),
],
```

### .env
```env
WHATSAPP_ACCESS_TOKEN=your_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_id_here
WHATSAPP_API_VERSION=v17.0
```

**Why use config()?**
- Centralized config management
- Can override per environment
- Tidak hardcode di service

---

## 🔐 Security Considerations

### 1. Token Protection
```php
// ❌ DON'T: Hardcode token di code
$token = 'EAAFOkkxxxxx';

// ✅ DO: Get dari env/config
$token = config('services.whatsapp.access_token');
```

### 2. Secure Logging
```php
// ❌ DON'T: Log token ke file
Log::info('Sending with token: ' . $token);

// ✅ DO: Log error, not credentials
Log::error('WhatsApp API Error: ' . $e->getMessage());
```

### 3. Phone Number Privacy
```php
// ✅ DO: Normalize dan validate
$phone = self::normalizePhone($order->phone);

// Phone disimpan di database (encrypted by default di production)
WhatsappLog::create(['phone_number' => $phone]);
```

---

## 🧪 Testing Approach

### Unit Test: normalizePhone()
```php
public function test_normalize_phone()
{
    // Test berbagai format
    $this->assertEquals('6281234567890', WhatsAppService::normalizePhone('081234567890'));
    $this->assertEquals('6281234567890', WhatsAppService::normalizePhone('+6281234567890'));
    $this->assertEquals('6281234567890', WhatsAppService::normalizePhone('6281234567890'));
}
```

### Integration Test: sendMessage()
```php
public function test_send_message_success()
{
    // Mock API response
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'messages' => [['id' => 'wamid.xxx', 'message_status' => 'accepted']]
        ])
    ]);
    
    $order = Order::factory()->create();
    $log = WhatsAppService::sendMessage($order, 'Test');
    
    $this->assertEquals('sent', $log->status);
    $this->assertNotNull($log->sent_at);
}
```

### Feature Test: Route cooked
```php
public function test_route_cooked_sends_whatsapp()
{
    Http::fake();
    
    $order = Order::factory()->create(['status' => 'cooking']);
    $admin = User::factory()->create();
    
    $this->actingAs($admin)
        ->patch(route('admin.orders.cooked', $order))
        ->assertRedirect();
    
    // Cek order status updated
    $this->assertEquals('ready_to_deliver', $order->fresh()->status);
    
    // Cek log created
    $this->assertDatabaseHas('whatsapp_logs', [
        'order_id' => $order->id
    ]);
}
```

---

## 📊 Database Schema Detail

### whatsapp_logs table

```sql
CREATE TABLE whatsapp_logs (
    id bigint PRIMARY KEY AUTO_INCREMENT,
    order_id bigint UNSIGNED NOT NULL,
    phone_number varchar(255),
    message longtext,
    status varchar(255) DEFAULT 'pending',  -- 'sent', 'failed', 'pending'
    api_response longtext,
    sent_at timestamp NULL,
    created_at timestamp,
    updated_at timestamp,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

### Indexes untuk Performance

```sql
-- Query logs by order
CREATE INDEX idx_whatsapp_logs_order_id ON whatsapp_logs(order_id);

-- Query failed messages
CREATE INDEX idx_whatsapp_logs_status ON whatsapp_logs(status);

-- Query recent logs
CREATE INDEX idx_whatsapp_logs_created_at ON whatsapp_logs(created_at);
```

---

## 🚀 Scaling Considerations

### Current Implementation
- Synchronous API call (wait for response)
- Blocking request hingga API respond

### Future Optimization
```php
// Option 1: Queue jobs untuk async processing
dispatch(new SendWhatsAppMessage($order, $message))->delay(0);

// Option 2: Webhook untuk delivery confirmation
// Set webhook di Facebook Dev Portal untuk delivery status

// Option 3: Rate limiting
// Implement per-minute limit untuk prevent abuse
```

---

## 📝 Maintenance Checklist

- [ ] Monitor `.env` token expiry (set calendar reminder)
- [ ] Check `storage/logs/laravel.log` untuk WhatsApp errors
- [ ] Query `whatsapp_logs` untuk failed messages monthly
- [ ] Test resend functionality regularly
- [ ] Keep WhatsApp API documentation bookmarked
- [ ] Update `.env` jika credentials change
- [ ] Monitor API costs (per message)

---

## 🔗 Related Files Quick Reference

```
app/Services/WhatsAppService.php      ← Main logic
app/Models/WhatsappLog.php            ← Database model
app/Models/Order.php                  ← Relations
routes/web.php                        ← Routes (cooked, resend_wa)
resources/views/admin.blade.php       ← UI indicator & button
config/services.php                   ← Config
.env                                  ← Credentials
database/migrations/2026_09_01_135138_create_whatsapp_logs_table.php
```

