# ✅ FCM Notification System - Testing Checklist

## 🔧 Pre-Testing Setup

```bash
# 1. Build assets
[ ] npm run build

# 2. Clear cache
[ ] php artisan cache:clear
[ ] php artisan config:clear

# 3. Start server
[ ] php artisan serve
```

---

## 🧪 Test 1: System Check (Test Page)

**URL:** `http://localhost:8000/test-notification.html`

### Actions:
- [ ] Open test page
- [ ] Click "Check System Status"
- [ ] Verify: Browser Support = `Supported ✓` (green)
- [ ] Verify: Service Worker = `Active ✓` (green)
- [ ] Verify: Notification Permission = `granted` (green)
- [ ] Verify: FCM Token = `[token]...` (green)

### Expected Result:
✅ All status items should be green

---

## 🧪 Test 2: Permission Request

### Actions:
- [ ] Click "Request Notification Permission"
- [ ] Allow notification when browser prompts
- [ ] Check console for success logs

### Expected Console Logs:
```
✅ Service worker registered
✅ Service worker ready
📋 Permission result: granted
✅ FCM Token: [token]
📋 Token copied to clipboard
```

### Expected Result:
✅ Permission granted, token obtained

---

## 🧪 Test 3: Browser Notification Test

### Actions:
- [ ] Click "Test Browser Notification"
- [ ] Notification should appear immediately

### Expected Result:
✅ Windows notification appears with:
- Title: "Test Notification"
- Body: "This is a test notification from Arradea FCM Test"
- Icon: Arradea logo

---

## 🧪 Test 4: Service Worker Check

### Actions:
- [ ] Click "Check Service Worker Details"
- [ ] Review console output

### Expected Console Logs:
```
📝 Service Worker 1:
  Scope: http://localhost:8000/
  Active: Yes
  State: activated
  Script URL: http://localhost:8000/firebase-messaging-sw.js

✅ Page is controlled by service worker
```

### Expected Result:
✅ Service Worker is active and controlling page

---

## 🧪 Test 5: Foreground Notification (Main App)

### Setup:
- [ ] Login to main app: `http://localhost:8000/login`
- [ ] Open browser console (F12)
- [ ] Verify initialization logs appear

### Expected Initialization Logs:
```
✅ Firebase initialized successfully
✅ Firebase Messaging initialized
✅ Service Worker registered successfully
✅ FCM Token obtained successfully
✅ Foreground message handler setup complete
```

### Send Test Notification:
```bash
# In terminal:
php artisan tinker
```

```php
# In tinker (replace YOUR_USER_ID):
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

### Expected Browser Console Logs:
```
================================================================================
📬 FOREGROUND MESSAGE RECEIVED
================================================================================
Full payload: {...}
Notification object: {...}
Data object: {...}
🔔 Processing notification:
  Title: 🔔 Foreground Test
  Body: This should appear while app is open
✅ Showing Arradea toast notification
🔔 Creating browser notification...
✅ Browser notification created successfully
```

### Expected Result:
- [ ] Notification appears in browser
- [ ] Toast message appears (if implemented)
- [ ] Console shows all expected logs

---

## 🧪 Test 6: Background Notification

### Setup:
- [ ] Keep app open in browser
- [ ] Minimize browser OR switch to another tab
- [ ] Keep DevTools open (detach if needed)

### Send Test Notification:
```bash
# In terminal:
php artisan tinker
```

```php
# In tinker (replace YOUR_USER_ID):
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

### Expected Result:
- [ ] Windows notification appears
- [ ] Notification shows in Windows Action Center
- [ ] Click notification opens browser

### Check Service Worker Console:
- [ ] Open DevTools → Application → Service Workers
- [ ] Click on "firebase-messaging-sw.js"
- [ ] Verify logs:

```
================================================================================
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
================================================================================
Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: 🌙 Background Test
```

---

## 🧪 Test 7: Laravel Backend Logs

### Check Laravel Log:
```bash
tail -f storage/logs/laravel.log | grep -A 20 "SENDING FCM"
```

### Expected Log Output:
```
================================================================================
📤 SENDING FCM NOTIFICATION
================================================================================
Title: [notification title]
Body: [notification body]
Icon: null
Click Action: http://localhost:8000/
Data payload: {...}
Number of tokens: 1
Tokens: ["..."]
Final data payload: {...}
Message built successfully
Sending to FCM...
FCM Response:
  Total: 1
  Successful: 1
  Failed: 0
✅ Notification sent successfully
================================================================================
```

### Verification:
- [ ] Log shows "Successful: 1"
- [ ] Log shows "Failed: 0"
- [ ] No error messages

---

## 🔍 Debug Commands Checklist

### Browser Console:
```javascript
// 1. Check permission
[ ] Notification.permission
    Expected: "granted"

// 2. Check service worker
[ ] window.debugServiceWorkers()
    Expected: Shows active SW

// 3. Check FCM availability
[ ] window.Arradea?.notification
    Expected: Object with request() and isSupported()

// 4. Check controller
[ ] navigator.serviceWorker.controller
    Expected: ServiceWorker object

// 5. Test manual notification
[ ] new Notification('Test', { body: 'Test', icon: '/icons/logo-arradea.png' })
    Expected: Notification appears
```

### Terminal:
```bash
# 1. Check FCM tokens
[ ] php artisan tinker
    >>> App\Models\FcmToken::where('is_active', true)->count()
    Expected: > 0

# 2. Check user tokens
[ ] php artisan tinker
    >>> App\Models\User::find(YOUR_ID)->fcmTokens()->count()
    Expected: > 0
```

---

## ✅ Final Success Criteria

System is working correctly if ALL of these are true:

- [ ] `Notification.permission === "granted"`
- [ ] Service Worker is active (verified with `window.debugServiceWorkers()`)
- [ ] FCM token is saved in database
- [ ] Foreground notification appears when app is open
- [ ] Background notification appears when app is minimized
- [ ] Click notification opens correct URL
- [ ] Laravel log shows "successful: 1"
- [ ] Browser console shows all expected logs
- [ ] Service Worker console shows background message logs
- [ ] No errors in any console

---

## 🐛 Troubleshooting Checklist

### If permission is denied:
- [ ] Click 🔒 in address bar
- [ ] Reset notification permission
- [ ] Refresh page (Ctrl+R)
- [ ] Request permission again

### If Service Worker not active:
- [ ] Hard refresh (Ctrl+Shift+R)
- [ ] Run `window.debugServiceWorkers()`
- [ ] Check console for errors
- [ ] Verify file exists: `/firebase-messaging-sw.js`

### If no notification appears:
- [ ] Check `Notification.permission`
- [ ] Test basic notification: `new Notification('Test', {body: 'Test'})`
- [ ] Check Windows notification settings
- [ ] Check browser notification settings
- [ ] Disable Do Not Disturb mode

### If token not saved:
- [ ] Check network tab for `/save-fcm-token` request
- [ ] Check Laravel log for errors
- [ ] Verify CSRF token exists
- [ ] Regenerate token: `window.Arradea?.notification?.request()`

---

## 📊 Test Results Summary

| Test | Status | Notes |
|------|--------|-------|
| System Check | [ ] Pass / [ ] Fail | |
| Permission Request | [ ] Pass / [ ] Fail | |
| Browser Test | [ ] Pass / [ ] Fail | |
| Service Worker | [ ] Pass / [ ] Fail | |
| Foreground Notification | [ ] Pass / [ ] Fail | |
| Background Notification | [ ] Pass / [ ] Fail | |
| Laravel Logs | [ ] Pass / [ ] Fail | |

**Overall Status:** [ ] ✅ All Pass / [ ] ❌ Some Failed

**Test Date:** _______________  
**Tested By:** _______________  
**Browser:** _______________  
**OS:** _______________

---

## 📝 Notes

Additional observations or issues:

```
[Write any additional notes here]
```

---

**Checklist Version:** 1.0  
**Last Updated:** 2026-05-22  
**Estimated Test Time:** 10-15 minutes
