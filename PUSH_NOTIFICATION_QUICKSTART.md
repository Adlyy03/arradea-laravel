# 🚀 Push Notification - Quick Start Guide

Panduan cepat untuk mengaktifkan push notification dalam 5 menit!

## ⚡ Quick Setup (5 Menit)

### 1. Firebase Setup (2 menit)

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Create new project atau pilih existing project
3. Klik ⚙️ Settings > Project settings
4. Pilih tab "Cloud Messaging"
5. Copy **VAPID key** (klik "Generate key pair" jika belum ada)
6. Pilih tab "Service accounts"
7. Klik "Generate new private key" dan download JSON file

### 2. Environment Setup (1 menit)

1. Copy file JSON ke `storage/app/firebase/`
2. Edit `.env` dan tambahkan:

```env
FIREBASE_CREDENTIALS=storage/app/firebase/your-file.json
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-api-key
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abc123
FIREBASE_MEASUREMENT_ID=G-ABC123
FIREBASE_VAPID_KEY=your-vapid-key
```

**Cara cepat dapat semua nilai:**
- Buka Firebase Console > Project Settings > General
- Scroll ke "Your apps" > Web app
- Copy dari "Firebase SDK snippet" > Config

### 3. Update Service Worker (1 menit)

Edit `public/firebase-messaging-sw.js` baris 7-14, ganti dengan config Firebase Anda:

```javascript
const firebaseConfig = {
    apiKey: "AIza...",
    authDomain: "your-project.firebaseapp.com",
    projectId: "your-project",
    storageBucket: "your-project.appspot.com",
    messagingSenderId: "123456789",
    appId: "1:123456789:web:abc123",
    measurementId: "G-ABC123"
};
```

### 4. Build & Test (1 menit)

```bash
# Build assets
npm run build

# Test notification
# 1. Login ke aplikasi
# 2. Buka: http://localhost/notifications/test-page
# 3. Klik "Request Permission"
# 4. Klik "Send Test"
```

## ✅ Verification Checklist

- [ ] Firebase project created
- [ ] Service account JSON downloaded
- [ ] `.env` file updated with all Firebase credentials
- [ ] `firebase-messaging-sw.js` updated with Firebase config
- [ ] Assets built with `npm run build`
- [ ] Test page accessible at `/notifications/test-page`
- [ ] Permission request works
- [ ] Test notification received

## 🎯 Usage Examples

### Send Notification in Controller

```php
use App\Services\PushNotificationService;

class YourController extends Controller
{
    protected $pushNotification;

    public function __construct(PushNotificationService $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    public function someAction()
    {
        $user = auth()->user();
        
        // Send notification
        $this->pushNotification->sendToUser(
            $user,
            'Hello!',
            'This is a test notification',
            ['type' => 'test'],
            asset('images/icon.png'),
            url('/dashboard')
        );
    }
}
```

### Send to All Users

```php
$this->pushNotification->sendToAll(
    'Announcement',
    'Important update for all users',
    ['type' => 'announcement'],
    asset('images/icon.png'),
    url('/announcements')
);
```

## 🔧 Common Issues

### "No token yet" di test page
- Pastikan sudah klik "Request Permission"
- Check browser console untuk error
- Pastikan VAPID key benar

### Notification tidak muncul
- Check permission status (harus "granted")
- Check service worker registered (DevTools > Application > Service Workers)
- Check Firebase config di service worker

### Error 500 saat send notification
- Check Firebase credentials file exists
- Check `.env` FIREBASE_CREDENTIALS path benar
- Check Laravel logs di `storage/logs/`

## 📱 Test on Mobile

1. Deploy ke server dengan HTTPS
2. Buka website di mobile browser
3. Add to Home Screen (PWA)
4. Test notification

## 🎨 Customization

### Change Notification Icon

Edit di controller:

```php
$this->pushNotification->sendToUser(
    $user,
    'Title',
    'Body',
    [],
    asset('images/your-custom-icon.png'), // <-- Change this
    url('/your-page')
);
```

### Change Notification Sound

1. Download sound file (MP3, < 100KB)
2. Save as `public/sounds/notification.mp3`
3. Done! Sound will play automatically

### Disable Sound

User dapat disable sound di `/notifications/settings`

## 📚 Next Steps

- Read full documentation: `PUSH_NOTIFICATION_SETUP.md`
- Customize notification UI
- Add notification to more events (orders, messages, etc.)
- Setup notification scheduling
- Monitor notification analytics

## 🆘 Need Help?

1. Check `PUSH_NOTIFICATION_SETUP.md` for detailed docs
2. Check Firebase Console for error logs
3. Check browser console for JavaScript errors
4. Check Laravel logs: `storage/logs/laravel.log`

---

**Happy Coding! 🎉**
