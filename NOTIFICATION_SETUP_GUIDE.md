# 🔔 Notification Setup Guide - PENTING!

## ⚠️ MASALAH YANG DITEMUKAN

**Seller tidak menerima notifikasi karena:**
- ❌ Seller belum request notification permission
- ❌ Seller tidak punya FCM token aktif
- ❌ Browser seller belum setup untuk menerima notifikasi

---

## ✅ SOLUSI - Setup Notification untuk Seller

### Step 1: Login sebagai Seller
1. Buka browser (Chrome/Edge recommended)
2. Login dengan akun **Seller** (contoh: Reza)
3. Pastikan sudah login sebagai seller

### Step 2: Allow Notification Permission
Saat pertama kali login, browser akan menampilkan popup:
```
[Website] wants to show notifications
[Block] [Allow]
```

**KLIK "ALLOW"** ✅

### Step 3: Verify Token Saved
Buka browser console (F12) dan cek log:
```
✅ FCM Token obtained successfully
✅ FCM token berhasil disimpan ke backend
```

### Step 4: Verify in Database
Jalankan script:
```bash
php check-fcm-tokens.php
```

Expected output:
```
User: Reza (ID: 25)
  Role: Seller
  Active tokens: 1  ← Harus ada minimal 1!
```

---

## 🧪 Testing After Setup

### Test 1: Create Order (Buyer)
1. Login sebagai **Buyer**
2. Buat order baru
3. **Seller harus menerima notifikasi** "🛒 Pesanan Baru!"

### Test 2: Upload Payment (Buyer)
1. Login sebagai **Buyer**
2. Upload bukti pembayaran
3. **Seller harus menerima notifikasi** "💳 Bukti Pembayaran Diterima"

### Test 3: Chat Message
1. Kirim pesan di chat
2. **Recipient harus menerima notifikasi** "💬 Pesan dari {name}"

---

## 🔍 Troubleshooting

### Problem: Notification permission tidak muncul

**Solution 1: Manual Request**
1. Buka browser console (F12)
2. Jalankan:
```javascript
window.Arradea?.notification?.request()
```
3. Klik "Allow" saat popup muncul

**Solution 2: Reset Permission**
1. Klik icon 🔒 di address bar
2. Site settings → Notifications → Allow
3. Refresh page (Ctrl+R)

### Problem: Token tidak tersimpan

**Check console for errors:**
```javascript
// Should see:
✅ FCM Token obtained successfully
✅ FCM token berhasil disimpan ke backend
```

**If error, check:**
1. CSRF token exists: `document.querySelector('meta[name="csrf-token"]')`
2. Network tab: `/save-fcm-token` request should return 200
3. Laravel log: `storage/logs/laravel.log`

### Problem: Masih tidak menerima notifikasi

**Verify checklist:**
- [ ] Seller sudah login
- [ ] Notification permission = "granted"
- [ ] FCM token tersimpan (cek dengan `php check-fcm-tokens.php`)
- [ ] Service Worker aktif (cek dengan `window.debugServiceWorkers()`)
- [ ] Browser tidak dalam mode "Do Not Disturb"
- [ ] Windows notification settings enabled

---

## 📊 Current Status

**From check-fcm-tokens.php:**
```
Total users with FCM tokens: 0  ← MASALAH!

Seller: Reza (ID: 25)
  Active tokens: 0  ← HARUS > 0!
```

**What needs to happen:**
1. ✅ Seller (Reza) login
2. ✅ Allow notification permission
3. ✅ FCM token saved
4. ✅ Test order creation
5. ✅ Notification appears!

---

## 🎯 Quick Commands

### Check FCM Tokens
```bash
php check-fcm-tokens.php
```

### Check Recent Logs
```bash
Get-Content storage/logs/laravel.log -Tail 50 | Select-String "FCM"
```

### Test Notification (After token saved)
```bash
php artisan tinker
```

```php
$seller = App\Models\User::find(25); // Reza
$service = app(App\Services\PushNotificationService::class);
$service->sendToUser(
    $seller,
    '🧪 Test Notification',
    'This is a test message',
    ['type' => 'test']
);
```

---

## ✅ Success Criteria

System is working when:
1. ✅ `php check-fcm-tokens.php` shows active tokens > 0
2. ✅ Buyer creates order → Seller gets notification
3. ✅ Notification appears without refresh
4. ✅ Click notification opens correct page

---

## 📝 Important Notes

### For Each User (Buyer & Seller):
- **Must login at least once** to get FCM token
- **Must allow notification permission**
- **Token is browser-specific** (Chrome token ≠ Edge token)
- **Token is device-specific** (Desktop ≠ Mobile)

### For Testing:
- **Both buyer and seller** need FCM tokens
- **Test in same browser** or different browsers
- **Check logs** after each action
- **Verify tokens** before testing

---

## 🚀 Next Steps

1. **Login sebagai Seller (Reza)**
2. **Allow notification permission**
3. **Verify token saved:** `php check-fcm-tokens.php`
4. **Test order creation**
5. **Notification should appear!** 🎉

---

**Status:** ⚠️ **WAITING FOR SELLER SETUP**

**Action Required:** Seller needs to login and allow notifications!
