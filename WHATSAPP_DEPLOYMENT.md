# WhatsApp Integration - Deployment & Go-Live Checklist

## 🎯 Pre-Deployment

### ✅ Code Verification
- [ ] Verify `app/Services/WhatsAppService.php` exists
- [ ] Verify `app/Models/WhatsappLog.php` exists
- [ ] Verify Order model memiliki relations ke whatsappLogs
- [ ] Verify routes di `routes/web.php`:
  - [ ] `admin.orders.cooked` route ada
  - [ ] `admin.orders.resend_wa` route ada
- [ ] Verify admin view menampilkan indikator WhatsApp
- [ ] Verify `config/services.php` punya whatsapp config

### ✅ Database
- [ ] Migration `create_whatsapp_logs_table` sudah ada
- [ ] Run migrations: `php artisan migrate`
- [ ] Verify table `whatsapp_logs` exist di database
- [ ] Verify foreign key ke `orders` table

### ✅ Environment Setup
- [ ] `.env` file sudah memiliki WhatsApp variables:
  ```env
  WHATSAPP_ACCESS_TOKEN=
  WHATSAPP_PHONE_NUMBER_ID=
  WHATSAPP_API_VERSION=v17.0
  ```
- [ ] Values masih kosong (akan diisi nanti)
- [ ] `.env` file di-add ke `.gitignore` (don't commit secrets!)

### ✅ Dependencies
- [ ] Laravel >= 10.0 (check `composer.json`)
- [ ] Guzzle HTTP client available (via Laravel)
- [ ] Database drivers ready (MySQL)

---

## 🔐 Security Checklist (Before Go-Live)

### ✅ Credentials Security
- [ ] Access token TIDAK hardcoded di file code
- [ ] Access token HANYA di `.env` file
- [ ] `.env` file NOT in repository (.gitignore active)
- [ ] Phone Number ID TIDAK hardcoded
- [ ] Credentials only read at runtime via `config()`

### ✅ API Communication
- [ ] Using HTTPS for WhatsApp API calls (automatic via URL)
- [ ] Bearer token properly sent in Authorization header
- [ ] Request payload sanitized (order data safe)

### ✅ Data Protection
- [ ] Phone numbers stored in database (can be encrypted in production)
- [ ] API responses stored in logs (JSON format)
- [ ] Sensitive error messages NOT shown to users
- [ ] Error logs only visible to admins/developers

### ✅ Access Control
- [ ] Routes protected with `->middleware('auth')`
- [ ] Only authenticated admins can trigger WhatsApp
- [ ] No public/unauthenticated endpoint untuk WhatsApp

### ✅ Logging & Audit
- [ ] All WhatsApp sends logged to database
- [ ] Log includes: order_id, phone, message, status, timestamp
- [ ] Admin dashboard menampilkan log history
- [ ] Failed attempts tracked untuk manual intervention

---

## 📋 Get WhatsApp API Credentials

### Step 1: Developer Account
- [ ] Visit: https://developers.facebook.com/
- [ ] Login dengan Facebook account (use business account)
- [ ] Verify email if needed

### Step 2: Create/Select App
- [ ] Create new app → type "Business"
- [ ] App name: "Foodie Express" (atau sesuai)
- [ ] Add WhatsApp product

### Step 3: WhatsApp Setup
- [ ] Go to: Products → WhatsApp → Getting Started
- [ ] Click "Create Account" or select existing Business Account
- [ ] Verify phone number (punya nomor sendiri atau gunakan customer's)
- [ ] Follow wizard

### Step 4: Generate Credentials
**Access Token**:
- [ ] Go to: Settings → System Users
- [ ] Create new System User (if needed)
- [ ] Generate new access token
- [ ] Select app permission: `whatsapp_business_messaging`
- [ ] Copy token (full value)

**Phone Number ID**:
- [ ] Go to: WhatsApp → Phone Numbers
- [ ] Copy "Phone Number ID" (bukan phone number-nya)

**API Version**:
- [ ] Default: `v17.0` (lihat docs untuk latest)

### Step 5: Verify Credentials Working
- [ ] Test send message via API (curl/Postman):
  ```bash
  curl -X POST \
    "https://graph.facebook.com/v17.0/{PHONE_NUMBER_ID}/messages" \
    -H "Authorization: Bearer {ACCESS_TOKEN}" \
    -H "Content-Type: application/json" \
    -d '{
      "messaging_product":"whatsapp",
      "to":"62812345678",
      "type":"text",
      "text":{"body":"Test message"}
    }'
  ```
- [ ] Response harus: `{"messages":[{"id":"...","message_status":"accepted"}]}`

### Step 6: Update .env
- [ ] Copy Access Token → `.env` WHATSAPP_ACCESS_TOKEN
- [ ] Copy Phone Number ID → `.env` WHATSAPP_PHONE_NUMBER_ID
- [ ] Set API Version → `.env` WHATSAPP_API_VERSION (default: v17.0)

---

## 🧪 Testing Before Go-Live

### Test 1: Local Environment
```bash
# Clear config cache (if cached)
php artisan config:cache

# Test via Tinker
php artisan tinker

# Test normalization
> App\Services\WhatsAppService::normalizePhone('081234567890')
# Expected: '6281234567890'

# Test send (with valid credentials)
> $order = App\Models\Order::first()
> App\Services\WhatsAppService::sendMessage($order, 'Test message')

# Check log
> App\Models\WhatsappLog::latest()->first()
```

### Test 2: Create Test Order
- [ ] Login admin dashboard
- [ ] Create test order via frontend
- [ ] Set order status to "cooking"
- [ ] Admin: Click "Selesai Masak"
- [ ] Verify:
  - [ ] Order status berubah ke "Siap Diantar"
  - [ ] Dashboard menampilkan 🟢 "WhatsApp terkirim"
  - [ ] Database `whatsapp_logs` punya record
  - [ ] Pembeli terima pesan WhatsApp

### Test 3: Phone Format
- [ ] Test dengan berbagai format nomor:
  - [ ] `081234567890` → harus berhasil
  - [ ] `+6281234567890` → harus berhasil
  - [ ] `6281234567890` → harus berhasil
- [ ] Cek di `whatsapp_logs.phone_number` semuanya dalam format `62xxx`

### Test 4: Prevent Duplikat
- [ ] Order dengan status "Siap Diantar" yang sudah pernah kirim
- [ ] Refresh page, coba klik tombol lagi
- [ ] Verify pesan HANYA terkirim 1x (not duplicate)
- [ ] Check database `whatsapp_logs` - hanya 1 record dengan status 'sent'

### Test 5: Manual Resend
- [ ] Buat order, trigger WhatsApp
- [ ] Set token di .env ke value invalid
- [ ] Buat order baru, trigger WhatsApp → harus FAIL
- [ ] Dashboard menampilkan 🔴 "WhatsApp gagal"
- [ ] Fix token di .env
- [ ] Klik tombol "Kirim Ulang WA"
- [ ] Verify berhasil: 🟢 "WhatsApp terkirim"

### Test 6: Error Handling
- [ ] Disconnect internet → trigger WhatsApp
- [ ] Verify: Error gracefully handled, no crash
- [ ] Status tetap updated (order ke ready_to_deliver)
- [ ] Log dibuat dengan status 'failed'
- [ ] Admin bisa retry via "Kirim Ulang WA"

### Test 7: Concurrent Requests
- [ ] 2 browser tabs, 2 admin users
- [ ] Tab 1: Klik "Selesai Masak" untuk Order A
- [ ] Tab 2: Klik "Selesai Masak" untuk Order B (bersamaan)
- [ ] Verify: Tidak ada race condition, data consistent

---

## 🚀 Production Deployment

### Step 1: Database Migration
```bash
# SSH ke production server
ssh user@production.com

# Navigate ke project directory
cd /path/to/web_penjualan

# Run migrations
php artisan migrate --force

# Verify table created
php artisan tinker
> Schema::hasTable('whatsapp_logs')
# Expected: true
```

### Step 2: Update .env
```bash
# Edit .env di production
nano .env

# Update dengan credentials REAL:
WHATSAPP_ACCESS_TOKEN=EAAFOkkxxxxxxxxxxxx
WHATSAPP_PHONE_NUMBER_ID=102234567890123
WHATSAPP_API_VERSION=v17.0

# Save & exit (Ctrl+O, Ctrl+X)
```

### Step 3: Verify Configuration
```bash
php artisan config:cache
php artisan config:clear

# Verify credentials loaded
php artisan tinker
> config('services.whatsapp.access_token')
# Expected: EAAFOkkxxxxxxxxxxxx (first 20 chars)
```

### Step 4: Test in Production
```bash
# Test via production Tinker
php artisan tinker

# Get real order dari database
> $order = App\Models\Order::where('status', 'cooking')->first()

# Send message
> App\Services\WhatsAppService::sendMessage($order, 'Production test message')

# Verify log
> App\Models\WhatsappLog::latest()->first()
```

### Step 5: Smoke Test
- [ ] Login admin dashboard production
- [ ] Create real test order
- [ ] Ubah status ke "cooking"
- [ ] Klik "Selesai Masak"
- [ ] Verify pembeli terima WhatsApp
- [ ] Verify dashboard menampilkan status ✅

### Step 6: Monitoring Setup
```bash
# Setup log monitoring
tail -f storage/logs/laravel.log | grep -i whatsapp

# Setup alerts (optional, via monitoring service):
# - Alert jika rate of failed > 10% per hour
# - Alert jika API response time > 5 seconds
# - Alert jika credentials error
```

---

## 📊 Post-Deployment Verification

### ✅ Admin Dashboard
- [ ] WhatsApp indikator visible di orders list
- [ ] "Kirim Ulang WA" button visible untuk failed messages
- [ ] Success messages menampilkan WhatsApp status

### ✅ Database
```sql
-- Check table exists
SELECT * FROM whatsapp_logs LIMIT 1;

-- Check recent messages
SELECT order_id, status, created_at FROM whatsapp_logs 
ORDER BY created_at DESC LIMIT 10;

-- Check failed messages
SELECT * FROM whatsapp_logs 
WHERE status = 'failed' 
ORDER BY created_at DESC;
```

### ✅ Logging
```bash
# Check error logs
tail -100 storage/logs/laravel.log | grep -i "error\|failed\|whatsapp"

# Verify no permission errors
tail -100 storage/logs/laravel.log | grep -i "permission\|denied"
```

### ✅ Real Usage
- [ ] Monitor pertama 24 jam: lihat log untuk issues
- [ ] Monitor failed rate: acceptable < 5%
- [ ] Monitor response time: acceptable < 3 seconds
- [ ] Monitor customer feedback: apakah terima pesan

---

## 🆘 Rollback Plan (Jika Ada Issue)

### Temporary Disable WhatsApp
```bash
# Edit .env
WHATSAPP_ACCESS_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=

# Restart (atau reload config)
php artisan config:clear
```

### Revert Code
```bash
# Jika ada bug di code
git revert <commit-id>
git push origin main
```

### Database Cleanup (Jika Perlu)
```bash
# Delete logs dari testing
DELETE FROM whatsapp_logs WHERE created_at > NOW() - INTERVAL 1 DAY AND status = 'failed';

# Backup before delete!
# mysqldump -u user -p database > backup.sql
```

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue**: Token expired
```
Error: Invalid access token
Solution: Generate new token dari Facebook Dev Portal, update .env
```

**Issue**: Phone number invalid
```
Error: Recipient invalid phone number
Solution: Check format (must 62xxx), verify number registered di WhatsApp Business
```

**Issue**: Rate limit
```
Error: Rate limit exceeded
Solution: Implement throttling, space out messages, contact WhatsApp support untuk increase limit
```

**Issue**: Pesan tidak terkirim tapi no error
```
Debug: Cek api_response di whatsapp_logs table
Check: Apakah customer number punya WhatsApp active
Check: Apakah message format valid (no special chars)
```

---

## 📋 Ongoing Maintenance

### Daily Tasks
- [ ] Check failed WhatsApp messages
  ```sql
  SELECT COUNT(*) FROM whatsapp_logs WHERE status = 'failed' AND created_at > NOW() - INTERVAL 1 DAY;
  ```
- [ ] Monitor error logs
  ```bash
  tail -100 storage/logs/laravel.log | grep -i error
  ```

### Weekly Tasks
- [ ] Review WhatsApp logs for trends
- [ ] Test manual resend functionality
- [ ] Check token validity (check expiry date)

### Monthly Tasks
- [ ] Full audit of WhatsApp logs
- [ ] Review costs (API charges)
- [ ] Check WhatsApp API version updates
- [ ] Backup database (including whatsapp_logs)

### Quarterly Tasks
- [ ] Security audit
- [ ] Performance review
- [ ] Update documentation
- [ ] Token refresh (if needed)

---

## ✅ Final Checklist Before Live

- [ ] Code review complete
- [ ] Database migrated successfully
- [ ] .env configured dengan real credentials
- [ ] All test cases passed
- [ ] Admin trained on feature
- [ ] Customer communication done (jika perlu)
- [ ] Monitoring setup active
- [ ] Backup strategy in place
- [ ] Support team briefed
- [ ] Documentation reviewed

---

## 🎉 READY TO GO LIVE!

Ketika semua checklist sudah completed, sistem siap untuk production use.

**Post Go-Live**:
1. Monitor real-time untuk first 24 hours
2. Be ready untuk quick fixes/rollback
3. Gather customer feedback
4. Iterate based on feedback

Good luck! 🚀
