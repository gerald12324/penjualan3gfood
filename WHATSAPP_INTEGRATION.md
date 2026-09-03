# WhatsApp API Integration Documentation

## Overview
Sistem ini terintegrasi dengan WhatsApp Business Platform API untuk mengirim notifikasi otomatis kepada pembeli ketika status pesanan berubah menjadi "Siap Diantar".

## Arsitektur Sistem

### Flow Pesanan
```
MENUNGGU 
  → DITERIMA (accepted)
  → SEDANG DIMASAK (cooking)
  → SIAP DIANTAR (ready_to_deliver) ← WhatsApp dikirim di sini
  → DIANTAR (delivering) 
  → SELESAI (completed)
```

### Saat Status Berubah ke "Siap Diantar"
1. Admin menekan tombol "Selesai Masak"
2. Backend update status order menjadi `ready_to_deliver`
3. Backend mengirim pesan WhatsApp ke pembeli
4. Log pengiriman disimpan di database table `whatsapp_logs`
5. Admin melihat notifikasi sukses/gagal di dashboard

## Konfigurasi WhatsApp API

### 1. Dapatkan Credentials dari Facebook Developer Portal

1. **Buka Facebook Developers**: https://developers.facebook.com
2. **Login atau buat akun** (gunakan akun Facebook bisnis)
3. **Buat aplikasi baru**:
   - Pilih tipe aplikasi: "Business"
   - Nama: Bebas (misal: "Foodie Express")
4. **Setup WhatsApp**:
   - Di sidebar, pilih "WhatsApp" → "Getting Started"
   - Pilih business account atau buat baru
   - Ikuti wizard untuk verifikasi nomor telepon
5. **Dapatkan credentials**:
   - **Access Token**: Di bagian "System User Access Tokens"
   - **Phone Number ID**: Di bagian "Phone Numbers"
   - **API Version**: Gunakan versi stabil terbaru (default: v17.0)

### 2. Konfigurasi .env File

Edit file `.env` dan isi dengan credentials yang didapat:

```env
# WhatsApp Business Platform Integration
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_API_VERSION=v17.0
```

**⚠️ PENTING:**
- JANGAN commit file `.env` ke repository
- JANGAN share access token dengan siapa pun
- Token akan langsung digunakan saat production

### 3. Testing di Development

Untuk testing tanpa mengirim SMS nyata:

1. **Development Mode**:
   - Biarkan `WHATSAPP_ACCESS_TOKEN` kosong atau gunakan dummy token
   - Sistem akan log error tapi tidak crash
   - Pengguna akan melihat notifikasi: "Gagal kirim notifikasi WhatsApp"

2. **Mock Testing**:
   - Lihat file log di `storage/logs/laravel.log`
   - Pesan akan tercatat sebagai failed dengan error detail

## Fitur-Fitur

### 1. Pengiriman Otomatis
- Ketika admin mengklik "Selesai Masak", pesan WhatsApp otomatis dikirim
- Hanya dikirim **sekali saja** per pesanan (prevent duplikat)
- Jika page di-refresh, pesan tidak akan dikirim lagi

### 2. Normalisasi Nomor Telepon
Nomor telepon otomatis dikonversi ke format internasional:
```
081234567890  →  6281234567890
```

Mendukung format:
- `08xxxxxxxxxx` (Indonesia)
- Format sudah dengan kode negara: `62xxxxxxxxxx`

### 3. Indikator Status
Di admin dashboard, setiap pesanan menampilkan status WhatsApp:
- 🟢 **Terkirim** (hijau) - Pesan berhasil sampai ke API
- 🔴 **Gagal** (merah) - Gagal kirim dengan detail error

### 4. Tombol Resend
Jika pengiriman gagal (misal: internet error, token invalid), admin bisa:
- Klik tombol "Kirim Ulang WA"
- Sistem akan retry pengiriman ke API
- Hasil akan ditampilkan di dashboard

## Database Schema

### Table: whatsapp_logs

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint | Primary key |
| order_id | bigint | Foreign key ke orders |
| phone_number | string | Nomor pembeli (format international) |
| message | text | Isi pesan yang dikirim |
| status | string | 'sent' atau 'failed' |
| api_response | text | Response lengkap dari WhatsApp API |
| sent_at | timestamp | Waktu pesan berhasil terkirim |
| created_at | timestamp | Waktu record dibuat |
| updated_at | timestamp | Waktu record diupdate |

### Contoh Log Sukses
```json
{
  "order_id": 1,
  "phone_number": "6281234567890",
  "message": "Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan.",
  "status": "sent",
  "api_response": "{\"messages\":[{\"id\":\"wamid.xxxxx\",\"message_status\":\"accepted\"}]}",
  "sent_at": "2026-09-01 15:30:45"
}
```

### Contoh Log Gagal
```json
{
  "order_id": 1,
  "phone_number": "6281234567890",
  "message": "Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan.",
  "status": "failed",
  "api_response": "{\"error\":{\"message\":\"Invalid access token\"}}",
  "sent_at": null
}
```

## Alur Kode

### 1. Route Handler: admin.orders.cooked
**File**: `routes/web.php`

```php
Route::middleware('auth')->patch('/admin/orders/{order}/cooked', function (Order $order) {
    if ($order->status === 'cooking') {
        DB::transaction(function () use ($order) {
            // 1. Update status pesanan
            $order->update(['status' => 'ready_to_deliver', 'cooked_at' => now()]);
            
            // 2. Prepare pesan
            $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
            
            // 3. Cek apakah sudah pernah kirim (prevent duplikat)
            $existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
            
            if (!$existingLog) {
                // 4. Kirim WhatsApp
                WhatsAppService::sendMessage($order, $message);
            }
        });
        
        // 5. Return response
        $latestLog = $order->latestWhatsappLog;
        if ($latestLog && $latestLog->status === 'sent') {
            return back()->with('success', 'Pesanan berhasil diubah menjadi Siap Diantar dan notifikasi WhatsApp berhasil dikirim.');
        } else {
            return back()->with('success', 'Pesanan selesai dimasak dan siap diantar. (Gagal kirim notifikasi WhatsApp)');
        }
    }
    return back();
})->name('admin.orders.cooked');
```

### 2. Service: WhatsAppService
**File**: `app/Services/WhatsAppService.php`

**normalizePhone()**: Konversi format nomor
```php
// Input: "08123456789" → Output: "6281234567890"
// Input: "6281234567890" → Output: "6281234567890"
```

**sendMessage()**: Kirim pesan ke API
```php
// 1. Ambil credentials dari config/.env
// 2. Normalkan nomor pembeli
// 3. Prepare payload sesuai WhatsApp Cloud API spec
// 4. POST ke https://graph.facebook.com/{version}/{phoneId}/messages
// 5. Simpan log ke database
// 6. Return log record
```

### 3. Model: WhatsappLog
**File**: `app/Models/WhatsappLog.php`

Menyimpan record setiap pengiriman:
- Hubungan ke Order via `order_id` foreign key
- Otomatis simpan timestamp `sent_at` jika berhasil
- API response disimpan as JSON untuk debugging

## Pesan WhatsApp

### Template Pesan
```
Pesanan #{order_code} sudah siap diantar. Terima kasih sudah memesan.
```

### Contoh Pesan Nyata
```
Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan.
```

### Customization Pesan
Untuk mengubah template, edit di `routes/web.php` pada route `admin.orders.cooked`:

```php
$message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
```

Bisa ditambah:
```php
$message = "Pesanan #{$order->order_code} sudah siap diantar. Total: Rp " . number_format($order->total, 0, ',', '.') . ". Terima kasih sudah memesan!";
```

## Troubleshooting

### Pesan Tidak Terkirim

**Problem**: Status WhatsApp menunjukkan "Gagal"

**Solusi**:
1. **Cek Access Token**
   - Pastikan token valid dan tidak expired
   - Token biasanya berlaku lama, tapi bisa di-rotate

2. **Cek Phone Number ID**
   - Gunakan nomor yang didaftarkan di WhatsApp Business Account
   - Pastikan format: hanya angka, tanpa kode negara di depan

3. **Cek Koneksi Internet**
   - Pastikan server bisa reach `https://graph.facebook.com`

4. **Lihat Error Log**
   - Buka `storage/logs/laravel.log`
   - Cari error message dari WhatsApp API
   - Contoh error: "Invalid access token", "Phone not registered", dll

5. **Test Secara Manual**
   - Buka `storage/logs/laravel.log`
   - Lihat `api_response` di table `whatsapp_logs`
   - Response akan detail error dari Facebook API

### Nomor Pembeli Error

**Problem**: Pesan gagal karena nomor tidak valid

**Solusi**:
1. Pastikan nomor pembeli dimulai dengan 08 atau sudah 62
2. Panjang nomor harus 10-15 digit
3. Periksa input di checkout form

### Duplikat Pengiriman

**Problem**: Pesan dikirim berkali-kali

**Solusi**: Sistem sudah prevent duplikat dengan check:
```php
$existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
if (!$existingLog) {
    // Send
}
```

Jika tetap terjadi duplikat, cek:
1. Apakah ada dua admin yang klik tombol bersamaan
2. Refresh database, lihat table `whatsapp_logs`
3. Lihat log file untuk error details

## API Endpoints yang Digunakan

### WhatsApp Cloud API: Send Message
```
POST https://graph.facebook.com/v17.0/{PHONE_NUMBER_ID}/messages
Authorization: Bearer {ACCESS_TOKEN}

Body:
{
  "messaging_product": "whatsapp",
  "to": "6281234567890",
  "type": "text",
  "text": {
    "body": "Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan."
  }
}

Response (Success):
{
  "messages": [
    {
      "id": "wamid.xxxxx",
      "message_status": "accepted"
    }
  ]
}

Response (Error):
{
  "error": {
    "message": "Invalid access token",
    "type": "OAuthException",
    "code": 190
  }
}
```

## Security Checklist

- ✅ Access token disimpan di .env, tidak di source code
- ✅ Access token tidak ditampilkan di frontend
- ✅ Nomor pembeli dinormalisasi sebelum kirim
- ✅ Database transaction untuk konsistensi
- ✅ Log semua pengiriman untuk audit trail
- ✅ Prevent duplikat dengan check existing log
- ✅ Error handling graceful (tidak crash)
- ✅ Admin bisa manual resend jika gagal

## Maintenance & Monitoring

### Daily Checks
```sql
-- Cek pesan yang gagal hari ini
SELECT * FROM whatsapp_logs 
WHERE status = 'failed' 
AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;

-- Cek total pesan terkirim
SELECT COUNT(*) as total_sent 
FROM whatsapp_logs 
WHERE status = 'sent';

-- Cek pesanan tanpa log WhatsApp (mungkin belum diproses)
SELECT o.id, o.order_code 
FROM orders o 
LEFT JOIN whatsapp_logs wl ON o.id = wl.order_id 
WHERE o.status = 'ready_to_deliver' 
AND wl.id IS NULL;
```

### Log Monitoring
Monitor file `storage/logs/laravel.log` untuk entries:
- `WhatsApp API Error: ...`
- Successful send (lihat table `whatsapp_logs`)

### Token Refresh
WhatsApp access tokens bisa expire:
1. Set reminder untuk check token validity setiap bulan
2. Update .env dengan token baru jika expire
3. Tidak perlu restart server, config dibaca setiap request

## Roadmap Fitur Tambahan (Opsional)

- [ ] Multiple status notifications (saat accepted, saat delivering)
- [ ] Rich media messages (gambar menu, struk)
- [ ] Two-way messaging (customer reply)
- [ ] Webhook untuk status delivery report
- [ ] Template messages (pre-approved templates)
- [ ] Notification untuk kurir (status assigned, ready for pickup)
