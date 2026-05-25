# 🚀 FCM Quick Test Guide

## 📋 Pre-Test Checklist

```bash
# 1. Build assets
npm run build

# 2. Clear cache
php artisan cache:clear
php artisan config:clear

# 3. Start server
php artisan serve
```

---

## 🧪 Test Sequence

### Test 1: System Check (2 min)
1. Open: `http://localhost:8000/test-notification.html`
2. Click: **"Check System Status"**
3. ✅ Verify all status items are green

**Expected:**
- Browser Support: `Supported ✓` (green)
- Service Worker: `Active ✓` (green)
- Notification Permission: `granted` (green)
- FCM Token: `[token]...` (green)

---

### Test 2: Permission Request (1 min)
1. Click: **"Request Notification Permission"**
2. Allow notification when prompted
3. Check console for token

**Expected Console Log:**
```
✅ Service worker registered
✅ Service worker ready
📋 Permission result: granted
✅ FCM Token: [long token string]
📋 Token copied to clipboard
```

---

### Test 3: Browser Notification (30 sec)
1. Click: **"Test Browser Notification"**
2. Notification should appear immediately

**Expected:**
- Windows notification appears
- Title: "Test Notification"
- Body: "This is a test notification from Arradea FCM Test"
- Icon: Arradea logo

---

### Test 4: Service Worker Check (1 min)
1. Click: **"Check Service Worker Details"**
2. Review console output

**Expected Console Log:**
```
📝 Service Worker 1:
  Scope: http://localhost:8000/
  Active: Yes
  State: activated
  Script URL: http://localhost:8000/firebase-messaging-sw.js

✅ Page is controlled by service worker
```

---

### Test 5: Foreground Notification (3 min)

#### A. Login to Main App
1. Open: `http://localhost:8000/login`
2. Login with your account
3. Open browser console (F12)

#### B. Check Initialization
Look for these logs:
```
✅ Firebase initialized successfully
✅ Firebase Messaging initialized
✅ Service Worker registered successfully
✅ FCM Token obtained successfully
✅ Foreground message handler setup complete
```

#### C. Send Test Notification
Open new terminal:
```bash
php artisan tinker
```

```php
$user = App\Models\User::find(YOUR_USER_ID);
$service = app(App\Services\PushNotificationService::class);
$result = $service->sendToUser(
    $user,
    '🔔 Foreground Test',
    'This should appear while app is open',
    ['type' => 'test'],
    null,
    url('/')
);
print_r($result);
```

#### D. Verify Notification
**Expected in Browser Console:**
```
================================================================================
📬 FOREGROUND MESSAGE RECEIVED
================================================================================
Full payload: {
  "notification": {
    "title": "🔔 Foreground Test",
    "body": "This should appear while app is open"
  },
  ...
}
🔔 Creating browser notification...
✅ Browser notification created successfully
```

**Expected in Browser:**
- Notification appears
- Toast message appears (if implemented)

---

### Test 6: Background Notification (3 min)

#### A. Prepare
1. Keep app open in browser
2. Minimize browser OR switch to another tab
3. Keep console open (detach DevTools if needed)

#### B. Send Notification
In terminal (tinker):
```php
$user = App\Models\User::find(YOUR_USER_ID);
$service = app(App\Services\PushNotificationService::class);
$result = $service->sendToUser(
    $user,
    '🌙 Background Test',
    'This should appear when app is minimized',
    ['type' => 'test'],
    null,
    url('/')
);
print_r($result);
```

#### C. Verify Notification
**Expected:**
- Windows notification appears
- Notification shows in Windows Action Center
- Click notification opens browser

**Check Service Worker Console:**
1. Open DevTools → Application → Service Workers
2. Click on "firebase-messaging-sw.js"
3. Look for:
```
================================================================================
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
================================================================================
Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: 🌙 Background Test
```

---

## 🐛 Quick Troubleshooting

### Issue: Permission not granted
```javascript
// In console:
Notification.requestPermission().then(console.log)
```
- Click 🔒 in address bar → Reset permissions
- Refresh page

### Issue: Service Worker not active
```javascript
// In console:
window.debugServiceWorkers()
```
- Hard refresh: Ctrl+Shift+R
- Check for errors in console

### Issue: No FCM token
```javascript
// In console:
window.Arradea?.notification?.request()
```
- Check permission is granted
- Check service worker is active

### Issue: Notification not appearing
```javascript
// Test basic notification:
new Notification('Test', { 
    body: 'Test', 
    icon: '/icons/logo-arradea.png' 
})
```
- Check Windows notification settings
- Check browser notification settings
- Check Do Not Disturb mode

---

## ✅ Success Checklist

- [ ] Test page shows all green status
- [ ] Permission granted successfully
- [ ] Test notification appears
- [ ] Service worker is active and controlling page
- [ ] Foreground notification appears when app is open
- [ ] Background notification appears when app is minimized
- [ ] Notification click opens correct URL
- [ ] Laravel logs show successful send
- [ ] Browser console shows all expected logs
- [ ] Windows notification center shows notifications

---

## 📊 Expected Results Summary

| Test | Expected Result | Time |
|------|----------------|------|
| System Check | All green ✅ | 30s |
| Permission | Granted + Token | 1m |
| Browser Test | Notification appears | 30s |
| SW Check | Active + Controlling | 1m |
| Foreground | Notification + Logs | 2m |
| Background | Notification + SW Logs | 2m |

**Total Test Time: ~7 minutes**

---

## 🔗 Quick Links

- Test Page: `http://localhost:8000/test-notification.html`
- Main App: `http://localhost:8000`
- Laravel Logs: `storage/logs/laravel.log`

---

## 📝 Debug Commands

```javascript
// Browser Console
Notification.permission                    // Check permission
window.debugServiceWorkers()              // Check SW status
window.Arradea?.notification?.request()   // Request permission
navigator.serviceWorker.controller        // Check controller
```

```bash
# Terminal
php artisan tinker                        # Open tinker
tail -f storage/logs/laravel.log         # Watch logs
```

---

**Ready to test? Start with Test 1! 🚀**
