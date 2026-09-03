# WhatsApp Integration - Test Flow Scenarios

## ✅ Test Case 1: Happy Path (Sukses Kirim)

### Prerequisite
- [ ] Admin sudah login
- [ ] Minimal 1 pesanan dengan status "cooking"
- [ ] `WHATSAPP_ACCESS_TOKEN` dan `WHATSAPP_PHONE_NUMBER_ID` sudah diisi di .env
- [ ] Token masih valid

### Steps
1. Buka Admin Dashboard → Pesanan → Cari order dengan status "Sedang Dimasak"
2. Klik tombol "Selesai Masak"
3. Page reload dengan success message

### Expected Result
✅ **Success Message**:
```
"Pesanan berhasil diubah menjadi Siap Diantar dan notifikasi WhatsApp berhasil dikirim."
```

✅ **Status Update**:
- Order status berubah dari "Sedang Dimasak" → "Siap Diantar"
- Di dashboard, indikator berubah menjadi 🟢 "WhatsApp terkirim"

✅ **Database**:
```sql
-- Check whatsapp_logs table
SELECT * FROM whatsapp_logs 
WHERE order_id = {order_id} 
ORDER BY created_at DESC LIMIT 1;

-- Expected output:
-- status: 'sent'
-- api_response: JSON dengan "message_status": "accepted"
-- sent_at: (timestamp saat ini)
```

✅ **Log File**:
```
tail -f storage/logs/laravel.log
-- Tidak ada error, hanya info message
```

---

## ✅ Test Case 2: Token Invalid (Gagal Kirim)

### Prerequisite
- [ ] .env `WHATSAPP_ACCESS_TOKEN` diisi dengan token invalid/expired
- [ ] Minimal 1 pesanan dengan status "cooking"

### Steps
1. Buka Admin Dashboard → Pesanan
2. Klik "Selesai Masak" pada order tertentu
3. Perhatikan message dan dashboard

### Expected Result
✅ **Success Message** (UI masih success, tapi dengan info):
```
"Pesanan selesai dimasak dan siap diantar. (Gagal kirim notifikasi WhatsApp)"
```

✅ **Status Update**:
- Order status tetap berubah: "Sedang Dimasak" → "Siap Diantar"
- Di dashboard, indikator: 🔴 "WhatsApp gagal"

✅ **Database**:
```sql
SELECT * FROM whatsapp_logs 
WHERE order_id = {order_id} 
ORDER BY created_at DESC LIMIT 1;

-- Expected output:
-- status: 'failed'
-- api_response: JSON dengan error (misal: "Invalid access token")
-- sent_at: NULL
```

✅ **Log File**:
```
-- storage/logs/laravel.log akan berisi:
"WhatsApp API Error: {error_message}"
```

---

## ✅ Test Case 3: Phone Number Normalization

### Prerequisite
- [ ] 3+ orders dengan format nomor berbeda-beda
  - Order A: `081234567890`
  - Order B: `+6281234567890`
  - Order C: `6281234567890`

### Steps
1. Trigger WhatsApp send untuk setiap order (klik "Selesai Masak")
2. Cek log database untuk normalisasi

### Expected Result
✅ **Semua nomor berhasil dinormalisasi ke format 62**:
```sql
SELECT order_id, phone_number FROM whatsapp_logs;

-- Expected output:
-- order_id: 1, phone_number: '6281234567890'
-- order_id: 2, phone_number: '6281234567890'
-- order_id: 3, phone_number: '6281234567890'
```

---

## ✅ Test Case 4: Prevent Duplikat Pengiriman

### Prerequisite
- [ ] 1 order dengan status "Siap Diantar"
- [ ] Order sudah punya 1 log WhatsApp dengan status 'sent'
- [ ] Token valid

### Steps
1. Klik "Selesai Masak" pada order yang sama berkali-kali
2. Atau refresh page dan klik lagi

### Expected Result
✅ **Pesan HANYA dikirim SEKALI**:
```sql
SELECT COUNT(*) as total FROM whatsapp_logs 
WHERE order_id = {order_id} AND status = 'sent';

-- Expected output: 1 (bukan 2, 3, atau lebih)
```

✅ **Log File**:
- Tidak ada duplikat send attempt
- Transaksi konsisten

---

## ✅ Test Case 5: Manual Resend (Jika Gagal)

### Prerequisite
- [ ] 1 order dengan status "Siap Diantar"
- [ ] Order punya log WhatsApp dengan status 'failed'
- [ ] Update token di .env dengan token valid

### Steps
1. Buka Admin Dashboard → Pesanan
2. Cari order dengan indikator 🔴 "WhatsApp gagal"
3. Klik tombol "Kirim Ulang WA"

### Expected Result
✅ **Success Message**:
```
"Notifikasi WhatsApp berhasil dikirim ulang."
```

✅ **Status Update**:
- Indikator berubah dari 🔴 → 🟢 "WhatsApp terkirim"

✅ **Database**:
```sql
SELECT status FROM whatsapp_logs 
WHERE order_id = {order_id} 
ORDER BY created_at DESC;

-- Expected output: 
-- Row 1: 'failed'  (try pertama)
-- Row 2: 'sent'    (retry)
```

---

## ✅ Test Case 6: Pesan WhatsApp Format

### Prerequisite
- [ ] 1 order berhasil kirim
- [ ] Token valid

### Steps
1. Trigger WhatsApp send (klik "Selesai Masak")
2. Cek log database untuk format pesan

### Expected Result
✅ **Pesan sesuai template**:
```sql
SELECT message FROM whatsapp_logs 
WHERE order_id = {order_id};

-- Expected output:
-- "Pesanan #FE-{timestamp} sudah siap diantar. Terima kasih sudah memesan."
-- Contoh: "Pesanan #FE-26083111839 sudah siap diantar. Terima kasih sudah memesan."
```

✅ **Order Code ada**:
- Bukan hardcode "ORD-001"
- Menggunakan order_code asli dari database

---

## ✅ Test Case 7: Workflow Order Lengkap

### Prerequisite
- [ ] Fresh order dari customer
- [ ] Token valid

### Steps
1. Admin Dashboard → Pesanan
2. Order berstatus "Menunggu"
3. Klik "Terima Pesanan" → Status: "Diterima"
4. Klik "Mulai Masak" → Status: "Sedang Dimasak"
5. Klik "Selesai Masak" → Status: "Siap Diantar" + WhatsApp sent
6. Pilih kurir, klik "Atur" → Status: "Diantar"
7. Klik "Pesanan Diterima" → Status: "Selesai"

### Expected Result
✅ **Semua status berhasil diupdate**
✅ **WhatsApp HANYA dikirim di step 5**
✅ **Log WhatsApp terekam di database**
✅ **Dashboard menampilkan timeline semua perubahan**

---

## ✅ Test Case 8: Error Handling (Koneksi Error)

### Prerequisite
- [ ] Server bisa simulate offline (misal: disconnect WiFi sebentar)
- [ ] 1 order siap untuk send

### Steps
1. Putuskan koneksi internet server
2. Klik "Selesai Masak"
3. Perhatikan error handling

### Expected Result
✅ **Tidak ada crash**:
- Page still responsive
- Error message ditampilkan user-friendly

✅ **Status tetap update**:
- Order status tetap berubah ke "Siap Diantar"
- Hanya WhatsApp yang gagal

✅ **Log terekam**:
```sql
SELECT api_response FROM whatsapp_logs 
WHERE status = 'failed' AND order_id = {order_id};

-- Expected output:
-- JSON dengan error detail (misal: connection timeout)
```

---

## ✅ Test Case 9: Multiple Concurrent Requests

### Prerequisite
- [ ] 3+ orders siap di-process
- [ ] Token valid
- [ ] 2+ admin login simultaneously (2 browser tabs)

### Steps
1. Di tab 1: Klik "Selesai Masak" untuk Order A
2. Di tab 2: Klik "Selesai Masak" untuk Order B (bersamaan)
3. Tunggu response dari kedua tab

### Expected Result
✅ **Tidak ada race condition**:
- Kedua order berhasil diproses
- Kedua WhatsApp berhasil dikirim
- Database konsisten (no data corruption)

✅ **Log terekam lengkap**:
```sql
SELECT * FROM whatsapp_logs 
WHERE order_id IN (A_id, B_id);

-- Expected: 2 rows dengan status sesuai
```

---

## ✅ Test Case 10: Database Transaction Consistency

### Prerequisite
- [ ] Setup test dengan intentional interrupt database
- [ ] Token valid

### Scenario
Apa yang terjadi jika:
- Order status sudah update ke "ready_to_deliver"
- Tapi WhatsApp API error sebelum save log?

### Expected Result
✅ **Transaksi rollback atau data tetap konsisten**:
```sql
-- Kedua hal terjadi bersamaan (atomic):
-- 1. Order status update → ready_to_deliver
-- 2. WhatsAppLog created → dengan status

-- Atau keduanya gagal (rollback)
-- Tidak boleh: status updated tapi log tidak ada
```

---

## 🧪 Manual Testing Commands (Linux/Mac)

```bash
# Test normalisasi nomor via Tinker
php artisan tinker
> App\Services\WhatsAppService::normalizePhone('081234567890')
> App\Services\WhatsAppService::normalizePhone('+6281234567890')

# Check log
> App\Models\WhatsappLog::latest(10)->get()

# Create dummy order
> $order = App\Models\Order::latest()->first()
> App\Services\WhatsAppService::sendMessage($order, 'Test message')

# Exit Tinker
> exit
```

---

## 🧪 Manual Testing Commands (Windows PowerShell)

```powershell
# Buka Tinker
php artisan tinker

# Test normalisasi
[System.Reflection.Assembly]::LoadWithPartialName("System.Web") | Out-Null
App\Services\WhatsAppService::normalizePhone('081234567890')

# Check log
App\Models\WhatsappLog::latest(10)->get()

# Exit
exit
```

---

## 📋 Testing Checklist

Sebelum go live, pastikan semua test case sudah passed:

- [ ] Test Case 1: Happy Path ✅
- [ ] Test Case 2: Token Invalid ✅
- [ ] Test Case 3: Phone Normalization ✅
- [ ] Test Case 4: Prevent Duplikat ✅
- [ ] Test Case 5: Manual Resend ✅
- [ ] Test Case 6: Pesan Format ✅
- [ ] Test Case 7: Workflow Lengkap ✅
- [ ] Test Case 8: Error Handling ✅
- [ ] Test Case 9: Concurrent Requests ✅
- [ ] Test Case 10: Transaction Consistency ✅

---

## 🚀 Production Deployment Checklist

Sebelum deploy ke production:

- [ ] .env dengan token REAL sudah diset
- [ ] Database sudah di-backup
- [ ] All test cases passed
- [ ] Monitoring setup (log monitoring)
- [ ] Backup plan jika API error
- [ ] Customer communication (jika ada issue)
- [ ] Staff training (admin tahu fitur ini ada)
- [ ] Error handling tested
- [ ] Documentation ready untuk support team

---

## 📞 Quick Reference

| Scenario | Expected Behavior |
|----------|------------------|
| First time "Selesai Masak" | ✅ WhatsApp sent + log created |
| Retry same button | ✅ Pesan hanya sent 1x (duplikat prevented) |
| Token invalid | ✅ Status updated, WhatsApp failed, dapat resend |
| Concurrent requests | ✅ Atomic transaction, no race condition |
| Offline/network error | ✅ Graceful error, dapat retry manual |
| Resend after failed | ✅ Attempt baru, bisa succeed/fail |
