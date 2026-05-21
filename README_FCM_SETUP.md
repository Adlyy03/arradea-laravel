# 🔔 Setup Firebase Cloud Messaging (FCM) - Push Notification Browser

Panduan lengkap untuk mengaktifkan push notification browser menggunakan Firebase Cloud Messaging di Arradea Marketplace.

---

## 📋 Daftar Isi

1. [Konfigurasi Firebase Console](#1-konfigurasi-firebase-console)
2. [Konfigurasi Laravel](#2-konfigurasi-laravel)
3. [Struktur File](#3-struktur-file)
4. [Cara Menjalankan](#4-cara-menjalankan)
5. [Cara Menggunakan](#5-cara-menggunakan)
6. [Testing](#6-testing)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Konfigurasi Firebase Console

### Step 1: Buat/Buka Project Firebase

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Pilih project **arradea-marketplace** atau buat baru
3. Klik **Project Settings** (⚙️ icon)

### Step 2: Dapatkan Firebase Config

Di tab **General**, scroll ke bawah ke bagian **Your apps**, pilih **Web app** (</> icon):

```javascript
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "YOUR_APP_ID"
};
```

**Copy** `apiKey` dan `appId` untuk digunakan nanti.

### Step 3: Dapatkan VAPID Key

1. Masih di **Project Settings**
2. Klik tab **Cloud Messaging**
3. Scroll ke **Web Push certificates**
4. Klik **Generate key pair** (jika belum ada)
5. **Copy** VAPID key yang muncul

### Step 4: Dapatkan Server Key

1. Masih di tab **Cloud Messaging**
2. Di bagian **Cloud Messaging API (Legacy)**, klik **Manage API in Google Cloud Console**
3. **Enable** Cloud Messaging API jika belum aktif
4. Kembali ke Firebase Console
5. **Copy** Server Key di bagian **Cloud Messaging API (Legacy)**

---

## 2. Konfigurasi Laravel

### Step 1: Update File Firebase Config

Edit file `resources/js/firebase.js`:

```javascript
const firebaseConfig = {
    apiKey: "PASTE_YOUR_API_KEY_HERE",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "PASTE_YOUR_APP_ID_HERE"
};

// ...

const vapidKey = 'PASTE_YOUR_VAPID_KEY_HERE';
```

Edit file `public/firebase-messaging-sw.js`:

```javascript
const firebaseConfig = {
    apiKey: "PASTE_YOUR_API_KEY_HERE",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "PASTE_YOUR_APP_ID_HERE"
};
```

### Step 2: Update .env

Tambahkan di file `.env`:

```env
FCM_SERVER_KEY=PASTE_YOUR_SERVER_KEY_HERE
```

### Step 3: Jalankan Migration

```bash
php artisan migrate
```

Ini akan menambahkan kolom `fcm_token` ke tabel `users`.

### Step 4: Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

---

## 3. Struktur File

```
arradea-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── NotificationController.php    # Controller untuk FCM
│   └── Models/
│       └── User.php                          # Model User (updated)
├── database/
│   └── migrations/
│       └── 2026_05_20_223359_add_fcm_token_to_users_table.php
├── public/
│   └── firebase-messaging-sw.js              # Service Worker untuk background notification
├── resources/
│   └── js/
│       ├── app.js                            # Main JS (updated)
│       └── firebase.js                       # Firebase SDK & FCM logic
└── routes/
    └── web.php                               # Routes (updated)
```

---

## 4. Cara Menjalankan

### Development Mode

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Production Mode

```bash
# Build assets
npm run build

# Serve dengan web server (Apache/Nginx)
```

---

## 5. Cara Menggunakan

### A. Request Permission (Otomatis)

Ketika user login, sistem akan otomatis meminta permission notifikasi setelah 3 detik.

Untuk mengaktifkan, tambahkan meta tag di layout blade:

```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @auth
    <meta name="user-authenticated" content="true">
    @endauth
</head>
```

### B. Request Permission (Manual)

Jika ingin manual trigger, panggil function di console browser:

```javascript
requestPermission();
```

### C. Kirim Notifikasi dari Laravel

#### Contoh 1: Notifikasi Pesanan Baru ke Seller

```php
use App\Http\Controllers\NotificationController;

// Di OrderController atau setelah order dibuat
$order = Order::create([...]);

NotificationController::notifyNewOrder($order);
```

#### Contoh 2: Notifikasi Status Order ke Buyer

```php
use App\Http\Controllers\NotificationController;

// Setelah seller update status order
$order->update(['status' => 'processing']);

NotificationController::notifyOrderStatusChange($order, 'processing');
```

#### Contoh 3: Notifikasi Custom

```php
use App\Http\Controllers\NotificationController;

NotificationController::sendPushNotification(
    $userId,                    // User ID atau array of IDs
    '🎉 Promo Spesial!',       // Title
    'Diskon 50% untuk semua produk hari ini!', // Body
    [                           // Data payload (optional)
        'type' => 'promo',
        'url' => route('buyer.products')
    ],
    'https://example.com/promo.jpg' // Image URL (optional)
);
```

#### Contoh 4: Kirim ke Multiple Users

```php
use App\Http\Controllers\NotificationController;

$userIds = [1, 2, 3, 4, 5];

NotificationController::sendPushNotification(
    $userIds,
    '📢 Pengumuman',
    'Sistem akan maintenance pada pukul 22:00 WIB',
    ['type' => 'announcement']
);
```

---

## 6. Testing

### Test 1: Cek Permission

1. Login ke aplikasi
2. Buka browser console (F12)
3. Cek apakah muncul log: `✅ Notification permission granted`
4. Cek apakah muncul log: `🔑 FCM Token: ...`

### Test 2: Cek Token Tersimpan

```sql
SELECT id, name, fcm_token FROM users WHERE fcm_token IS NOT NULL;
```

### Test 3: Kirim Test Notification

Buat route test di `routes/web.php`:

```php
Route::get('/test-fcm', function () {
    $user = auth()->user();
    
    $result = \App\Http\Controllers\NotificationController::sendPushNotification(
        $user->id,
        '🧪 Test Notification',
        'Ini adalah test notification dari Arradea!',
        ['type' => 'test']
    );
    
    return response()->json($result);
})->middleware('auth');
```

Akses: `http://localhost:8000/test-fcm`

### Test 4: Test Background Notification

1. Buka aplikasi di browser
2. Minimize atau switch ke tab lain
3. Kirim notifikasi menggunakan test route
4. Notifikasi harus muncul di system tray

---

## 7. Troubleshooting

### ❌ Permission Denied

**Solusi:**
1. Cek browser settings → Site settings → Notifications
2. Pastikan site diizinkan untuk mengirim notifikasi
3. Clear browser cache dan reload

### ❌ Service Worker Not Registered

**Solusi:**
1. Pastikan file `public/firebase-messaging-sw.js` ada
2. Akses langsung: `http://localhost:8000/firebase-messaging-sw.js`
3. Cek browser console untuk error
4. Pastikan HTTPS (atau localhost untuk development)

### ❌ FCM Token Tidak Tersimpan

**Solusi:**
1. Cek browser console untuk error
2. Pastikan CSRF token valid
3. Cek network tab untuk request `/save-fcm-token`
4. Pastikan user sudah login

### ❌ Notifikasi Tidak Muncul

**Solusi:**
1. Cek FCM Server Key di `.env`
2. Cek apakah Cloud Messaging API sudah enabled di Google Cloud Console
3. Cek log Laravel: `storage/logs/laravel.log`
4. Pastikan user memiliki `fcm_token` di database

### ❌ VAPID Key Error

**Solusi:**
1. Pastikan VAPID key sudah di-generate di Firebase Console
2. Copy paste dengan benar (tanpa spasi)
3. Reload page setelah update

### ❌ Import Error Firebase

**Solusi:**
```bash
# Reinstall Firebase
npm install firebase@latest

# Rebuild
npm run build
```

---

## 📚 Referensi

- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Web Push Notifications](https://firebase.google.com/docs/cloud-messaging/js/client)
- [Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)

---

## 🎯 Fitur yang Sudah Diimplementasi

✅ Request notification permission  
✅ Save FCM token ke database  
✅ Foreground notification handler  
✅ Background notification handler (Service Worker)  
✅ Send notification dari Laravel  
✅ Multiple recipients support  
✅ Custom data payload  
✅ Notification with image  
✅ Helper methods untuk common notifications:
  - New order notification
  - Order status change notification
  - Payment status notification
  - Chat message notification

---

## 🚀 Next Steps (Optional)

- [ ] Notification preferences (user bisa disable/enable)
- [ ] Notification history/inbox
- [ ] Scheduled notifications
- [ ] Notification analytics
- [ ] Push notification untuk mobile app (Android/iOS)

---

**Dibuat untuk:** Arradea Marketplace  
**Tech Stack:** Laravel 12 + Vite + Firebase JS SDK v10  
**Tanggal:** Mei 2026
