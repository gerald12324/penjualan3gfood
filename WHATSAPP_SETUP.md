# WhatsApp Integration - Quick Start Guide

## 🚀 Setup Cepat (5 Menit)

### 1. Update .env File
```bash
# File: .env

WHATSAPP_ACCESS_TOKEN=your_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_id_here
WHATSAPP_API_VERSION=v17.0
```

### 2. Cara Mendapatkan Credentials

1. Buka: https://developers.facebook.com/
2. Login dengan akun Facebook bisnis
3. Buat/pilih aplikasi → WhatsApp
4. Di "Getting Started", ikuti setup wizard
5. Di bagian "Phone Numbers", copy **Phone Number ID**
6. Di bagian "System Users", generate/copy **Access Token**
7. Paste ke .env file

### 3. Test Koneksi

```bash
# Di terminal project, buka Laravel Tinker
php artisan tinker

# Test normalisasi nomor
App\Services\WhatsAppService::normalizePhone('081234567890')
# Output: "6281234567890"

# Test kirim pesan (lihat di storage/logs/laravel.log)
$order = App\Models\Order::first();
App\Services\WhatsAppService::sendMessage($order, 'Test pesan');

# Lihat log
App\Models\WhatsappLog::where('order_id', 1)->latest()->first();
```

## 📊 Alur Kerja

### Admin Flow
1. ✅ Admin login ke dashboard
2. ✅ Terima pesanan → "Diterima"
3. ✅ Mulai masak → "Sedang Dimasak"
4. ✅ **Selesai masak → Status "Siap Diantar" + WhatsApp otomatis dikirim**
5. ✅ Pilih kurir → "Diantar"
6. ✅ Pesanan diterima → "Selesai"

### WhatsApp Flow
- Pesan dikirim: **Saat admin klik "Selesai Masak"**
- Status update: Status order → `ready_to_deliver`
- Pesan sent: "Pesanan #ORD-CODE sudah siap diantar. Terima kasih sudah memesan."
- Log disimpan: Di table `whatsapp_logs` untuk audit trail

### Jika Gagal Kirim
1. Admin lihat status 🔴 "WhatsApp gagal" di dashboard
2. Admin klik tombol "Kirim Ulang WA"
3. Sistem retry pengiriman
4. Lihat status berubah 🟢 "WhatsApp terkirim" atau tetap gagal

## 📁 File-File Penting

| File | Fungsi |
|------|--------|
| `app/Services/WhatsAppService.php` | Logic kirim WhatsApp |
| `app/Models/WhatsappLog.php` | Model log pengiriman |
| `database/migrations/2026_09_01_135138_create_whatsapp_logs_table.php` | Schema table |
| `config/services.php` | Config credentials |
| `.env` | Environment variables |
| `routes/web.php` | Route handlers (cooked, resend_wa) |
| `resources/views/admin.blade.php` | UI indikator status |

## 🔧 Customization

### Ubah Template Pesan
**File**: `routes/web.php` → Route `admin.orders.cooked`

```php
// Baris saat ini:
$message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";

// Bisa diubah jadi:
$message = "🎉 Pesanan #{$order->order_code} sudah siap diantar!\n"
         . "Total: Rp " . number_format($order->total, 0, ',', '.')
         . "\n\nTerima kasih sudah memesan di FoodieExpress!";
```

### Tambah Notifikasi Status Lain
Misalnya kirim WhatsApp juga saat "Diantar":

```php
// Di route admin.orders.courier, tambah:
if ($order->status === 'ready_to_deliver') {
    $order->update([...]);
    
    $message = "📍 Pesanan #{$order->order_code} sedang dalam perjalanan ke Anda.";
    WhatsAppService::sendMessage($order, $message);
}
```

## 🐛 Debug

### Lihat Log Pengiriman
```bash
# Terminal
tail -f storage/logs/laravel.log | grep -i whatsapp

# Atau di database
SELECT * FROM whatsapp_logs ORDER BY created_at DESC LIMIT 10;
```

### Cek Response API
```sql
-- Lihat error details dari WhatsApp
SELECT order_id, status, api_response, created_at 
FROM whatsapp_logs 
WHERE status = 'failed' 
ORDER BY created_at DESC;
```

### Common Errors

| Error | Solusi |
|-------|--------|
| `Invalid access token` | Token expired/salah. Update .env |
| `Invalid recipient` | Nomor pembeli tidak valid. Cek format |
| `Rate limit exceeded` | Terlalu banyak request. Tunggu beberapa menit |
| `Recipient invalid phone number` | Format nomor salah. Harus 08xxx atau 62xxx |

## ✅ Verification Checklist

- [ ] .env sudah ada `WHATSAPP_ACCESS_TOKEN`
- [ ] .env sudah ada `WHATSAPP_PHONE_NUMBER_ID`
- [ ] Table `whatsapp_logs` sudah dibuat (run migrations)
- [ ] User sudah bisa login ke admin
- [ ] Bisa create order dan ubah status
- [ ] Saat klik "Selesai Masak", status berubah ke "Siap Diantar"
- [ ] Log WhatsApp muncul di table `whatsapp_logs`
- [ ] Dashboard menampilkan indikator status WhatsApp

## 📞 Support

Dokumentasi lengkap: Baca file `WHATSAPP_INTEGRATION.md`

Untuk pertanyaan:
1. Cek log file: `storage/logs/laravel.log`
2. Cek database: `whatsapp_logs` table
3. Baca error response di `api_response` column
