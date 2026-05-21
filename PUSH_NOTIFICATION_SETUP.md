# 🔔 Push Notification System - Setup Guide

Sistem push notification modern untuk Laravel + Firebase Cloud Messaging (FCM) + PWA.

## 📋 Fitur Lengkap

### ✅ Desktop Notification
- ✓ Notification native di pojok kanan bawah Windows/macOS/Linux
- ✓ Menggunakan Service Worker Firebase
- ✓ Support background notification walau tab browser ditutup
- ✓ Icon, title, body, dan click action
- ✓ Saat notif diklik langsung membuka halaman tertentu
- ✓ Desain notification clean dan modern
- ✓ Sound notification opsional

### ✅ Mobile Notification
- ✓ Notification muncul seperti notifikasi HP asli Android/iPhone
- ✓ Support ketika website sudah di-install sebagai PWA
- ✓ Tetap menerima notif walau browser tidak dibuka
- ✓ Support vibration dan badge icon
- ✓ Support click redirect ke halaman tertentu

### ✅ Laravel Backend
- ✓ Simpan token FCM user ke database
- ✓ Endpoint kirim notification
- ✓ Support kirim notif ke semua user / user tertentu
- ✓ Gunakan Firebase Admin SDK (Kreait Laravel Firebase)

### ✅ Frontend
- ✓ Minta izin notification dengan UI modern popup
- ✓ Warning elegan jika user menolak notif
- ✓ Toast sukses jika berhasil subscribe
- ✓ JavaScript modular

### ✅ Service Worker
- ✓ File firebase-messaging-sw.js lengkap
- ✓ Handle foreground notification
- ✓ Handle background notification
- ✓ Handle notification click
- ✓ Auto focus tab jika website sudah terbuka

### ✅ UI/UX
- ✓ Desain modern dengan Tailwind CSS
- ✓ Responsive
- ✓ Animasi smooth
- ✓ Support dark mode

## 🚀 Setup Instructions

### 1. Firebase Project Setup

1. **Buat Firebase Project**
   - Kunjungi [Firebase Console](https://console.firebase.google.com/)
   - Klik "Add project" atau "Create a project"
   - Ikuti wizard setup

2. **Enable Cloud Messaging**
   - Di Firebase Console, pilih project Anda
   - Klik ⚙️ (Settings) > Project settings
   - Pilih tab "Cloud Messaging"
   - Copy **Server key** dan **Sender ID**

3. **Generate Web Credentials**
   - Di Project settings > Cloud Messaging
   - Scroll ke "Web configuration"
   - Klik "Generate key pair" untuk mendapatkan **VAPID key**

4. **Download Service Account JSON**
   - Di Project settings > Service accounts
   - Klik "Generate new private key"
   - Download file JSON (simpan di `storage/app/firebase/`)

### 2. Environment Configuration

Edit file `.env` dan tambahkan konfigurasi Firebase:

```env
# Firebase Cloud Messaging Configuration
FIREBASE_CREDENTIALS=storage/app/firebase/your-project-firebase-adminsdk.json
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-api-key
FIREBASE_AUTH_DOMAIN=your-project-id.firebaseapp.com
FIREBASE_STORAGE_BUCKET=your-project-id.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your-sender-id
FIREBASE_APP_ID=your-app-id
FIREBASE_MEASUREMENT_ID=G-XXXXXXXXXX
FIREBASE_VAPID_KEY=your-vapid-key
```

**Cara mendapatkan nilai-nilai ini:**
- Buka Firebase Console > Project Settings > General
- Scroll ke "Your apps" > Web app
- Copy semua nilai dari "Firebase SDK snippet" > Config

### 3. Update Vite Configuration

Edit `vite.config.js` untuk menambahkan environment variables:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    define: {
        'import.meta.env.VITE_FIREBASE_API_KEY': JSON.stringify(process.env.FIREBASE_API_KEY),
        'import.meta.env.VITE_FIREBASE_AUTH_DOMAIN': JSON.stringify(process.env.FIREBASE_AUTH_DOMAIN),
        'import.meta.env.VITE_FIREBASE_PROJECT_ID': JSON.stringify(process.env.FIREBASE_PROJECT_ID),
        'import.meta.env.VITE_FIREBASE_STORAGE_BUCKET': JSON.stringify(process.env.FIREBASE_STORAGE_BUCKET),
        'import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID': JSON.stringify(process.env.FIREBASE_MESSAGING_SENDER_ID),
        'import.meta.env.VITE_FIREBASE_APP_ID': JSON.stringify(process.env.FIREBASE_APP_ID),
        'import.meta.env.VITE_FIREBASE_MEASUREMENT_ID': JSON.stringify(process.env.FIREBASE_MEASUREMENT_ID),
        'import.meta.env.VITE_FIREBASE_VAPID_KEY': JSON.stringify(process.env.FIREBASE_VAPID_KEY),
    },
});
```

### 4. Update Service Worker Configuration

Edit `public/firebase-messaging-sw.js` dan ganti placeholder dengan nilai Firebase Anda:

```javascript
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID",
    measurementId: "YOUR_MEASUREMENT_ID"
};
```

### 5. Add Meta Tag for Authenticated Users

Tambahkan meta tag di layout blade Anda (misalnya `resources/views/layouts/app.blade.php`):

```blade
@auth
<meta name="user-authenticated" content="true">
@endauth
```

### 6. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 7. Test Notification

1. Login ke aplikasi
2. Kunjungi `/notifications/test-page`
3. Klik "Request Permission"
4. Klik "Send Test" untuk mengirim notifikasi test

## 📡 API Endpoints

### Store FCM Token
```
POST /notifications/token/store
Content-Type: application/json

{
    "token": "fcm-token-here",
    "device_type": "web",
    "device_name": "Chrome on Windows",
    "browser": "Chrome",
    "platform": "Windows"
}
```

### Delete FCM Token
```
POST /notifications/token/delete
Content-Type: application/json

{
    "token": "fcm-token-here"
}
```

### Send Test Notification
```
POST /notifications/test
```

### Get User Tokens
```
GET /notifications/tokens
```

### Send to Specific User (Admin Only)
```
POST /admin/notifications/send-to-user
Content-Type: application/json

{
    "user_id": 1,
    "title": "Hello!",
    "body": "This is a test notification",
    "data": {
        "type": "order",
        "order_id": 123
    },
    "icon": "https://example.com/icon.png",
    "click_action": "https://example.com/orders/123"
}
```

### Send to All Users (Admin Only)
```
POST /admin/notifications/send-to-all
Content-Type: application/json

{
    "title": "Announcement",
    "body": "Important update for all users",
    "data": {},
    "icon": "https://example.com/icon.png",
    "click_action": "https://example.com"
}
```

## 💻 Usage Examples

### Send Notification from Controller

```php
use App\Services\PushNotificationService;
use App\Models\User;

class OrderController extends Controller
{
    protected $pushNotification;

    public function __construct(PushNotificationService $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    public function createOrder(Request $request)
    {
        // Create order logic...
        $order = Order::create($request->all());

        // Send notification to user
        $this->pushNotification->sendToUser(
            $order->user,
            'Order Created',
            'Your order #' . $order->id . ' has been created successfully',
            [
                'type' => 'order',
                'order_id' => $order->id
            ],
            asset('images/order-icon.png'),
            route('buyer.orders.show', $order->id)
        );

        return response()->json(['success' => true]);
    }
}
```

### Send Notification to Multiple Users

```php
$userIds = [1, 2, 3, 4, 5];

$this->pushNotification->sendToUsers(
    $userIds,
    'New Product Available',
    'Check out our latest products!',
    ['type' => 'product'],
    asset('images/product-icon.png'),
    route('buyer.products')
);
```

### Send Notification to All Users

```php
$this->pushNotification->sendToAll(
    'System Maintenance',
    'The system will be under maintenance from 10 PM to 12 AM',
    ['type' => 'announcement'],
    asset('images/maintenance-icon.png'),
    route('home')
);
```

### Send to Topic

```php
// Subscribe users to topic
$tokens = ['token1', 'token2', 'token3'];
$this->pushNotification->subscribeToTopic($tokens, 'sellers');

// Send to topic
$this->pushNotification->sendToTopic(
    'sellers',
    'New Order Alert',
    'You have a new order!',
    ['type' => 'order'],
    asset('images/order-icon.png'),
    route('seller.orders')
);
```

## 🎨 Frontend Usage

### Initialize Notifications

```javascript
import notificationUI from './resources/js/notification-ui.js';

// Auto-initialize on page load
window.addEventListener('load', () => {
    notificationUI.initialize();
});
```

### Manual Permission Request

```javascript
import pushNotification from './resources/js/push-notification.js';

// Request permission
const granted = await pushNotification.requestPermission();

if (granted) {
    console.log('Permission granted!');
} else {
    console.log('Permission denied');
}
```

### Listen for Messages

```javascript
pushNotification.on('onMessageReceived', (payload) => {
    console.log('New message:', payload);
    // Handle message
});
```

### Send Test Notification

```javascript
const result = await pushNotification.sendTestNotification();
console.log(result);
```

## 🔧 Troubleshooting

### Notification tidak muncul

1. **Check browser support**
   - Chrome, Firefox, Edge, Opera support push notifications
   - Safari memerlukan konfigurasi tambahan

2. **Check permission status**
   - Pastikan user sudah grant permission
   - Check di browser settings

3. **Check service worker**
   - Buka DevTools > Application > Service Workers
   - Pastikan service worker registered

4. **Check Firebase config**
   - Pastikan semua environment variables sudah benar
   - Check Firebase Console untuk error logs

### Token tidak tersimpan

1. **Check CSRF token**
   - Pastikan meta tag csrf-token ada di HTML
   - Check network tab untuk error 419

2. **Check authentication**
   - User harus sudah login
   - Check middleware auth

### Background notification tidak bekerja

1. **Check service worker scope**
   - Service worker harus di root (/)
   - Check registration scope

2. **Check Firebase config di service worker**
   - Pastikan config sudah benar
   - Reload service worker

## 📱 PWA Integration

Untuk mendukung notification di PWA, pastikan:

1. **Manifest.json sudah ada**
```json
{
    "name": "Arradea Marketplace",
    "short_name": "Arradea",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#667eea",
    "icons": [
        {
            "src": "/images/icon-192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "/images/icon-512.png",
            "sizes": "512x512",
            "type": "image/png"
        }
    ]
}
```

2. **Service worker registered**
3. **HTTPS enabled** (required for PWA)

## 🎯 Best Practices

1. **Jangan spam notifikasi**
   - Kirim notifikasi hanya untuk event penting
   - Berikan opsi untuk disable notifikasi

2. **Personalisasi notifikasi**
   - Gunakan nama user di title/body
   - Kirim notifikasi yang relevan

3. **Handle errors gracefully**
   - Catch dan log semua errors
   - Berikan feedback ke user

4. **Test di berbagai device**
   - Desktop (Windows, macOS, Linux)
   - Mobile (Android, iOS)
   - Berbagai browser

5. **Monitor performance**
   - Track notification delivery rate
   - Monitor token refresh
   - Clean up invalid tokens

## 📚 Resources

- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Web Push Notifications](https://web.dev/push-notifications-overview/)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Notification API](https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API)

## 🆘 Support

Jika ada masalah atau pertanyaan:
1. Check dokumentasi di atas
2. Check Firebase Console untuk error logs
3. Check browser console untuk JavaScript errors
4. Check Laravel logs di `storage/logs/`

---

**Created with ❤️ for Arradea Marketplace**
