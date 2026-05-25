# 🔔 FCM Notification Debug Guide

## ✅ Perbaikan yang Sudah Dilakukan

### 1. **Service Worker Configuration** ✓
- ✅ Firebase config di `public/firebase-messaging-sw.js` sudah diupdate dengan credentials yang benar
- ✅ Background message handler sudah ditambahkan dengan logging lengkap
- ✅ Notification display menggunakan `self.registration.showNotification()`

### 2. **Foreground Message Handler** ✓
- ✅ Enhanced logging di `onMessage()` handler
- ✅ Manual browser notification menggunakan `new Notification()`
- ✅ Payload debugging yang lengkap
- ✅ Error handling yang lebih baik

### 3. **Laravel Backend Logging** ✓
- ✅ Detailed logging di `PushNotificationService.php`
- ✅ Payload tracking sebelum dikirim ke FCM
- ✅ Response tracking dari FCM

### 4. **Debug Tools** ✓
- ✅ Test page: `/test-notification.html`
- ✅ Service worker debug function: `window.debugServiceWorkers()`

---

## 🧪 Cara Testing

### Step 1: Build Assets
```bash
npm run build
# atau untuk development
npm run dev
```

### Step 2: Clear Cache & Restart
```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear

# Restart Laravel server
php artisan serve
```

### Step 3: Test di Browser

#### A. Menggunakan Test Page (Recommended)
1. Buka: `http://localhost:8000/test-notification.html`
2. Klik **"Check System Status"** - pastikan semua hijau ✅
3. Klik **"Request Notification Permission"** - izinkan notifikasi
4. Klik **"Test Browser Notification"** - harus muncul notifikasi
5. Klik **"Check Service Worker Details"** - pastikan SW aktif

#### B. Menggunakan Main App
1. Login ke aplikasi
2. Buka browser console (F12)
3. Cari log: `✅ Firebase Cloud Messaging initialized successfully`
4. Cari log: `✅ Foreground message handler setup complete`
5. Jalankan: `window.debugServiceWorkers()` di console
6. Pastikan output menunjukkan SW aktif

---

## 🔍 Debugging Checklist

### 1. Browser Permission
```javascript
// Di browser console:
console.log('Permission:', Notification.permission);
// Harus: "granted"
```

**Jika "denied":**
- Klik icon 🔒 di address bar
- Reset notification permission
- Refresh page
- Request permission lagi

### 2. Service Worker Status
```javascript
// Di browser console:
window.debugServiceWorkers();
```

**Expected output:**
```
✅ Page is controlled by service worker
   Controller script: http://localhost:8000/firebase-messaging-sw.js
```

**Jika NOT controlled:**
- Refresh page (Ctrl+R)
- Hard refresh (Ctrl+Shift+R)
- Check console untuk error

### 3. FCM Token
```javascript
// Di browser console:
console.log('Token:', window.Arradea?.notification);
```

**Jika undefined:**
- Check console untuk error
- Pastikan permission = "granted"
- Pastikan service worker aktif

### 4. Foreground Message
**Test dengan mengirim notifikasi dari backend:**

Di browser console, cari log:
```
📬 FOREGROUND MESSAGE RECEIVED
Full payload: {...}
🔔 Creating browser notification...
✅ Browser notification created successfully
```

**Jika tidak muncul:**
- Check `Notification.permission` = "granted"
- Check console untuk error
- Pastikan `onMessage()` handler terdaftar

### 5. Background Message
**Test dengan:**
1. Minimize browser atau switch ke tab lain
2. Kirim notifikasi dari backend
3. Notifikasi harus muncul di Windows notification center

**Check service worker console:**
- Buka DevTools → Application → Service Workers
- Klik "firebase-messaging-sw.js"
- Lihat console log

Expected log:
```
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
Full payload: {...}
🔔 Showing notification: [title]
```

---

## 🚀 Testing dari Backend

### Method 1: Artisan Tinker
```bash
php artisan tinker
```

```php
// Get user
$user = App\Models\User::find(1);

// Send notification
$service = app(App\Services\PushNotificationService::class);
$result = $service->sendToUser(
    $user,
    'Test Notification',
    'This is a test message from Laravel',
    ['type' => 'test'],
    null,
    url('/')
);

// Check result
print_r($result);
```

### Method 2: Test Route
Tambahkan route di `routes/web.php`:
```php
Route::get('/test-fcm', function() {
    $user = auth()->user();
    if (!$user) {
        return 'Please login first';
    }
    
    $service = app(App\Services\PushNotificationService::class);
    $result = $service->sendToUser(
        $user,
        '🔔 Test Notification',
        'Ini adalah test notification dari Arradea',
        ['type' => 'test', 'url' => url('/')],
        null,
        url('/')
    );
    
    return response()->json($result);
})->middleware('auth');
```

Akses: `http://localhost:8000/test-fcm`

---

## 📋 Expected Logs

### Frontend Console (Foreground)
```
✅ Firebase initialized successfully
✅ Firebase Messaging initialized
🔔 Requesting notification permission...
📋 Permission result: granted
✅ Service Worker registered successfully
✅ FCM Token obtained successfully
✅ Foreground message handler setup complete
================================================================================
📬 FOREGROUND MESSAGE RECEIVED
================================================================================
Full payload: {
  "notification": {
    "title": "Test Notification",
    "body": "This is a test message"
  },
  "data": {...}
}
🔔 Creating browser notification...
✅ Browser notification created successfully
```

### Service Worker Console (Background)
```
[firebase-messaging-sw.js] Service worker activated
================================================================================
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
================================================================================
Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: Test Notification
```

### Laravel Log (storage/logs/laravel.log)
```
================================================================================
📤 SENDING FCM NOTIFICATION
================================================================================
Title: Test Notification
Body: This is a test message
Number of tokens: 1
Message built successfully
Sending to FCM...
FCM Response:
  Total: 1
  Successful: 1
  Failed: 0
✅ Notification sent successfully
================================================================================
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Notification tidak muncul sama sekali
**Diagnosis:**
- Check `Notification.permission` → harus "granted"
- Check service worker status → harus "activated"
- Check FCM token → harus ada

**Solution:**
1. Reset browser notification permission
2. Hard refresh (Ctrl+Shift+R)
3. Request permission lagi
4. Check console untuk error

### Issue 2: Foreground works, Background tidak
**Diagnosis:**
- Service worker tidak aktif
- Service worker config salah
- Page tidak controlled by SW

**Solution:**
1. Check `window.debugServiceWorkers()`
2. Pastikan SW scope = "/"
3. Refresh page untuk activate SW
4. Check SW console untuk error

### Issue 3: Backend success tapi frontend tidak terima
**Diagnosis:**
- Token mismatch
- Payload format salah
- Network issue

**Solution:**
1. Check Laravel log untuk payload yang dikirim
2. Check FCM token di database vs frontend
3. Regenerate token:
```javascript
window.Arradea.notification.request()
```

### Issue 4: Browser notification permission denied
**Solution:**
1. Klik icon 🔒 di address bar
2. Site settings → Notifications → Allow
3. Refresh page
4. Request permission lagi

### Issue 5: Service Worker tidak register
**Check:**
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Registrations:', regs.length);
    regs.forEach(reg => console.log(reg.scope));
});
```

**Solution:**
1. Check file exists: `/firebase-messaging-sw.js`
2. Check console untuk error
3. Try manual register:
```javascript
navigator.serviceWorker.register('/firebase-messaging-sw.js', {
    scope: '/',
    updateViaCache: 'none'
}).then(reg => console.log('Registered:', reg));
```

---

## 🎯 Quick Debug Commands

### Browser Console Commands
```javascript
// 1. Check permission
Notification.permission

// 2. Check service workers
window.debugServiceWorkers()

// 3. Check FCM availability
window.Arradea?.notification

// 4. Request permission manually
window.Arradea?.notification?.request()

// 5. Test browser notification
new Notification('Test', { body: 'Test message', icon: '/logo.png' })

// 6. Check all registrations
navigator.serviceWorker.getRegistrations().then(console.log)

// 7. Check controller
navigator.serviceWorker.controller
```

### Laravel Artisan Commands
```bash
# Check FCM tokens in database
php artisan tinker
>>> App\Models\FcmToken::where('is_active', true)->count()

# Test send notification
php artisan tinker
>>> $user = App\Models\User::first();
>>> app(App\Services\PushNotificationService::class)->sendToUser($user, 'Test', 'Message');

# Check logs
tail -f storage/logs/laravel.log | grep FCM
```

---

## ✅ Success Criteria

Notification system berfungsi dengan baik jika:

1. ✅ **Permission granted**: `Notification.permission === "granted"`
2. ✅ **Service Worker active**: `window.debugServiceWorkers()` shows active SW
3. ✅ **FCM token obtained**: Token tersimpan di database
4. ✅ **Foreground notification**: Notifikasi muncul saat app terbuka
5. ✅ **Background notification**: Notifikasi muncul saat app minimize/tab lain
6. ✅ **Click action works**: Klik notifikasi membuka URL yang benar
7. ✅ **Laravel logs**: Backend log menunjukkan "successful: 1"
8. ✅ **Console logs**: Semua log debug muncul dengan benar

---

## 📞 Next Steps

Setelah semua perbaikan:

1. **Build assets**: `npm run build`
2. **Clear cache**: `php artisan cache:clear`
3. **Test dengan test page**: `/test-notification.html`
4. **Test foreground**: Kirim notif saat app terbuka
5. **Test background**: Kirim notif saat app minimize
6. **Check logs**: Browser console + Laravel log
7. **Report hasil**: Screenshot console logs + notification

---

## 🔗 Useful Links

- [Firebase Console](https://console.firebase.google.com/)
- [FCM Documentation](https://firebase.google.com/docs/cloud-messaging/js/client)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Notification API](https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API)

---

**Last Updated**: 2026-05-22
**Version**: 1.0
**Status**: Ready for Testing ✅
