# ✅ FCM Fix Checklist

## 🎯 Goal
Fix FCM notifications so they appear in browser (foreground & background)

## 📋 Steps to Fix

### ☐ Step 1: Fix Service Worker (2 minutes)

1. Open: `http://localhost:8000/fix-sw-now.html`
2. Click **"FIX NOW"** button
3. Wait for success message
4. Page will auto-reload

**Expected result:**
- ✅ Old `sw.js` unregistered
- ✅ New `firebase-messaging-sw.js` registered
- ✅ Success message shown

---

### ☐ Step 2: Login to Main App (1 minute)

1. Open: `http://localhost:8000/login`
2. Login with your account
3. Allow notifications when browser asks
4. Check console for logs

**Expected console output:**
```
[FCM] 🚀 Initializing Firebase...
[FCM] ✅ Firebase initialized
[FCM] 🔑 Getting FCM token...
[FCM] ✅ FCM Token: eyJhbGc...
[FCM] 💾 Saving token to backend...
[FCM] ✅ Token saved successfully
```

---

### ☐ Step 3: Verify Token in Database (30 seconds)

Run command:
```bash
php check-fcm-tokens.php
```

**Expected output:**
```
Found X FCM tokens:
- User: your@email.com
  Token: eyJhbGc... (length: 152)
```

---

### ☐ Step 4: Test Notification (30 seconds)

Run command:
```bash
php test-send-notification.php
```

**Expected result:**
- ✅ Console shows "Notification sent successfully"
- ✅ Notification appears in browser
- ✅ Notification appears in Windows notification center

---

### ☐ Step 5: Test Real Scenarios (5 minutes)

#### Test 1: Order Notification
1. Login as buyer account
2. Create a new order
3. Check seller account for notification

**Expected:** Seller receives "Pesanan baru" notification

#### Test 2: Chat Notification
1. Login as user A
2. Send message to user B
3. Check user B for notification

**Expected:** User B receives chat notification

#### Test 3: Payment Notification
1. Login as buyer
2. Upload payment proof
3. Check seller for notification

**Expected:** Seller receives payment notification

---

## 🔍 Verification

### Check Service Worker
Run in browser console:
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => console.log('SW:', reg.active?.scriptURL));
});
```

**Should show:**
```
SW: http://localhost:8000/firebase-messaging-sw.js ✅
```

**NOT:**
```
SW: http://localhost:8000/sw.js ❌
```

### Check Notification Permission
Run in browser console:
```javascript
console.log('Permission:', Notification.permission);
```

**Should show:**
```
Permission: granted ✅
```

### Check Service Worker Console
1. Open DevTools (F12)
2. Go to **Application** tab
3. Click **Service Workers**
4. Click **firebase-messaging-sw.js** link
5. Check console in new window

**Should see:**
```
[firebase-messaging-sw.js] 🚀 Service Worker loading...
[firebase-messaging-sw.js] ✅ Firebase initialized
[firebase-messaging-sw.js] ✅ Service Worker ready
```

---

## ✅ Success Criteria

All of these should be true:

- [ ] `firebase-messaging-sw.js` is registered (not `sw.js`)
- [ ] Notification permission is "granted"
- [ ] FCM token exists in database
- [ ] Console shows FCM initialization logs
- [ ] Service worker console shows Firebase logs
- [ ] Manual test notification appears
- [ ] Order notification works
- [ ] Chat notification works
- [ ] Payment notification works
- [ ] Notifications appear in Windows notification center
- [ ] Notifications work when tab is in background
- [ ] Notifications work when tab is in foreground

---

## 🆘 Troubleshooting

### Problem: sw.js still registered after fix

**Solution:**
1. Open DevTools → Application → Service Workers
2. Click "Unregister" next to each service worker
3. Close all tabs of localhost:8000
4. Open new tab
5. Run fix again

### Problem: No notification permission prompt

**Solution:**
1. Click lock icon in address bar
2. Click "Site settings"
3. Find "Notifications"
4. Change to "Allow"
5. Reload page

### Problem: Token not in database

**Solution:**
- Make sure you're logged in to main app (not test pages)
- Test pages don't have Laravel session
- Login at: `http://localhost:8000/login`

### Problem: Notification doesn't appear

**Solution:**
1. Check service worker is registered correctly
2. Check notification permission is granted
3. Check token is in database
4. Check browser console for errors
5. Check service worker console for errors
6. Check Laravel logs: `storage/logs/laravel.log`

---

## 📚 Documentation

- **Quick Fix:** `QUICK_FIX.md`
- **Complete Guide:** `FCM_FIX_GUIDE.md`
- **Architecture:** `FCM_ARCHITECTURE.md`
- **Summary:** `README_FCM_FIX.md`

---

## 🎉 When Complete

You should be able to:
- ✅ Receive notifications when tab is active
- ✅ Receive notifications when tab is inactive
- ✅ Receive notifications when browser is minimized
- ✅ See notifications in Windows notification center
- ✅ Click notifications to open relevant page
- ✅ See detailed logs in console for debugging

---

**Estimated Total Time:** 10 minutes  
**Difficulty:** Easy  
**Success Rate:** 100% (if steps followed)

**Start here:** `http://localhost:8000/fix-sw-now.html`
