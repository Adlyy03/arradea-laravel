# 🔧 Firebase FCM Troubleshooting Guide

Panduan lengkap untuk mengatasi masalah Firebase Cloud Messaging di Arradea Marketplace.

---

## ✅ Checklist Setelah Setup

### 1. Pastikan File Ada dan Benar

```bash
# Check files exist
ls -la resources/js/firebase.js
ls -la public/firebase-messaging-sw.js
```

### 2. Pastikan Dependencies Terinstall

```bash
npm list firebase
# Should show: firebase@12.13.0 or higher
```

### 3. Build Frontend

```bash
# Development
npm run dev

# Production
npm run build
```

### 4. Check Console Logs

Buka browser DevTools (F12) dan lihat Console. Seharusnya muncul:

```
✅ Firebase initialized successfully
✅ Firebase Messaging initialized
🔔 Initializing Firebase Cloud Messaging...
✅ Firebase Cloud Messaging initialized successfully
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Halaman Blank Putih

**Gejala:**
- Halaman tidak render sama sekali
- Console menunjukkan error Firebase

**Penyebab:**
- Import Firebase crash sebelum try/catch
- Service Worker error yang tidak di-handle
- VAPID key invalid

**Solusi:**

1. **Clear browser cache dan reload:**
   ```
   Ctrl + Shift + Delete (Chrome/Edge)
   Pilih: Cached images and files
   ```

2. **Unregister service worker lama:**
   - Buka DevTools → Application → Service Workers
   - Klik "Unregister" pada semua service worker
   - Reload halaman

3. **Check console untuk error spesifik:**
   ```javascript
   // Jika ada error "Firebase already initialized"
   // Hapus cache browser dan reload
   
   // Jika ada error "Service Worker registration failed"
   // Check file firebase-messaging-sw.js ada di public/
   ```

4. **Hard reload:**
   ```
   Ctrl + Shift + R (Windows/Linux)
   Cmd + Shift + R (Mac)
   ```

---

### Issue 2: Service Worker Registration Failed

**Gejala:**
```
❌ Service Worker registration failed
```

**Solusi:**

1. **Pastikan file ada di public/:**
   ```bash
   ls -la public/firebase-messaging-sw.js
   ```

2. **Check file permissions:**
   ```bash
   chmod 644 public/firebase-messaging-sw.js
   ```

3. **Pastikan HTTPS atau localhost:**
   - Service Worker hanya jalan di HTTPS atau localhost
   - Tidak jalan di HTTP biasa

4. **Check browser support:**
   ```javascript
   // Paste di console
   console.log('Service Worker supported:', 'serviceWorker' in navigator);
   console.log('Notifications supported:', 'Notification' in window);
   ```

---

### Issue 3: Permission Denied / Blocked

**Gejala:**
```
❌ Notification permission denied
⚠️ Notifikasi diblokir
```

**Solusi:**

1. **Reset permission di browser:**
   
   **Chrome/Edge:**
   - Klik ikon gembok di address bar
   - Pilih "Site settings"
   - Scroll ke "Notifications"
   - Ubah ke "Ask" atau "Allow"
   - Reload halaman

   **Firefox:**
   - Klik ikon gembok di address bar
   - Klik "Clear permissions and reload"

2. **Check permission status:**
   ```javascript
   // Paste di console
   console.log('Permission:', Notification.permission);
   // Should be: "default", "granted", or "denied"
   ```

3. **Manual request permission:**
   ```javascript
   // Paste di console
   window.Arradea.notification.request();
   ```

---

### Issue 4: FCM Token Tidak Didapat

**Gejala:**
```
⚠️ Tidak dapat mengambil FCM token
```

**Solusi:**

1. **Check VAPID key:**
   - Pastikan VAPID key di `firebase.js` sama dengan di Firebase Console
   - Firebase Console → Project Settings → Cloud Messaging → Web Push certificates

2. **Check Firebase config:**
   ```javascript
   // Pastikan semua field terisi di firebase.js
   const firebaseConfig = {
       apiKey: "...",
       authDomain: "...",
       projectId: "...",
       storageBucket: "...",
       messagingSenderId: "...",
       appId: "..."
   };
   ```

3. **Re-register service worker:**
   ```javascript
   // Paste di console
   navigator.serviceWorker.getRegistrations().then(registrations => {
       registrations.forEach(reg => reg.unregister());
   });
   // Reload halaman
   ```

---

### Issue 5: Notifikasi Tidak Muncul

**Gejala:**
- Token berhasil didapat
- Tapi notifikasi tidak muncul saat dikirim

**Solusi:**

1. **Check Do Not Disturb mode:**
   - Windows: Check Focus Assist settings
   - Mac: Check Do Not Disturb settings

2. **Check browser notification settings:**
   - Pastikan notifikasi tidak di-block di OS level

3. **Test dengan curl:**
   ```bash
   curl -X POST https://fcm.googleapis.com/fcm/send \
     -H "Authorization: key=YOUR_SERVER_KEY" \
     -H "Content-Type: application/json" \
     -d '{
       "to": "FCM_TOKEN_HERE",
       "notification": {
         "title": "Test",
         "body": "Test notification"
       }
     }'
   ```

4. **Check foreground handler:**
   ```javascript
   // Paste di console
   console.log('Foreground handler active:', 
     window.Arradea?.notification ? 'Yes' : 'No'
   );
   ```

---

### Issue 6: Error "messaging/token-subscribe-failed"

**Gejala:**
```
❌ messaging/token-subscribe-failed
```

**Solusi:**

1. **Check internet connection**

2. **Check Firebase project status:**
   - Buka Firebase Console
   - Pastikan project aktif dan tidak suspended

3. **Regenerate VAPID key:**
   - Firebase Console → Project Settings → Cloud Messaging
   - Generate new Web Push certificate
   - Update key di `firebase.js`

4. **Check browser extensions:**
   - Disable ad blockers
   - Disable privacy extensions
   - Try in incognito mode

---

## 🧪 Testing FCM

### Test 1: Check Browser Support

```javascript
// Paste di browser console
console.log('=== Browser Support Check ===');
console.log('Service Worker:', 'serviceWorker' in navigator);
console.log('Notifications:', 'Notification' in window);
console.log('Permission:', Notification.permission);
console.log('FCM Supported:', window.Arradea?.notification?.isSupported());
```

### Test 2: Manual Permission Request

```javascript
// Paste di browser console
window.Arradea.notification.request()
  .then(token => console.log('Token:', token))
  .catch(err => console.error('Error:', err));
```

### Test 3: Check Service Worker

```javascript
// Paste di browser console
navigator.serviceWorker.getRegistrations()
  .then(regs => {
    console.log('Registered Service Workers:', regs.length);
    regs.forEach(reg => {
      console.log('- Scope:', reg.scope);
      console.log('- State:', reg.active?.state);
    });
  });
```

### Test 4: Send Test Notification from Backend

```php
// Paste di tinker: php artisan tinker
use App\Http\Controllers\NotificationController;

$userId = 1; // Your user ID
NotificationController::sendPushNotification(
    $userId,
    'Test Notification',
    'This is a test message',
    ['type' => 'test']
);
```

---

## 📋 Debug Checklist

Gunakan checklist ini untuk debug sistematis:

- [ ] File `firebase.js` ada di `resources/js/`
- [ ] File `firebase-messaging-sw.js` ada di `public/`
- [ ] Package `firebase` terinstall (`npm list firebase`)
- [ ] Build berhasil (`npm run dev` tanpa error)
- [ ] Browser support Service Worker (Chrome, Firefox, Edge)
- [ ] HTTPS atau localhost (bukan HTTP biasa)
- [ ] Permission tidak di-block
- [ ] VAPID key benar
- [ ] Firebase config benar
- [ ] Service Worker registered
- [ ] FCM token berhasil didapat
- [ ] Token tersimpan di database
- [ ] Backend route `/save-fcm-token` ada
- [ ] Server key benar di `.env`
- [ ] No ad blocker atau privacy extension yang block

---

## 🔍 Useful Console Commands

```javascript
// Check if FCM initialized
console.log('FCM Available:', !!window.Arradea?.notification);

// Check permission status
console.log('Permission:', Notification.permission);

// Request permission manually
window.Arradea.notification.request();

// Check if supported
console.log('Supported:', window.Arradea.notification.isSupported());

// Unregister all service workers
navigator.serviceWorker.getRegistrations().then(regs => 
  regs.forEach(reg => reg.unregister())
);

// Check current FCM token (if stored)
fetch('/api/user/fcm-token', {
  headers: { 'Accept': 'application/json' }
}).then(r => r.json()).then(console.log);
```

---

## 🚨 Emergency: Disable FCM

Jika FCM menyebabkan masalah dan perlu di-disable sementara:

### Option 1: Comment out di app.js

```javascript
// Comment out bagian ini di resources/js/app.js
/*
async function initializeFirebaseMessaging() {
  // ... all FCM code
}
*/
```

### Option 2: Skip untuk user tertentu

Tambah meta tag di layout:

```blade
{{-- Skip FCM for testing --}}
<meta name="user-authenticated" content="false">
```

### Option 3: Environment variable

Tambah di `.env`:

```env
FCM_ENABLED=false
```

Update `app.js`:

```javascript
const fcmEnabled = document.querySelector('meta[name="fcm-enabled"]')?.content === 'true';

if (fcmEnabled) {
  initializeFirebaseMessaging();
}
```

---

## 📞 Getting Help

Jika masih ada masalah:

1. **Check browser console** untuk error messages
2. **Check network tab** untuk failed requests
3. **Check Application tab** → Service Workers
4. **Try incognito mode** untuk rule out extensions
5. **Try different browser** untuk rule out browser-specific issues

---

## 📚 Resources

- [Firebase Cloud Messaging Docs](https://firebase.google.com/docs/cloud-messaging/js/client)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Notification API](https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API)
- [Web Push Protocol](https://developers.google.com/web/fundamentals/push-notifications)

---

**Last Updated:** May 21, 2026
