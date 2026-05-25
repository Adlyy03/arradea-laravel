# 🔔 Notification Display Debug Guide

## ✅ Status Saat Ini

**WORKING:**
- ✅ FCM token generation
- ✅ Token saved to backend
- ✅ Firebase backend send success
- ✅ Notification permission granted
- ✅ Service worker active

**NOT WORKING:**
- ❌ Notification tidak muncul di browser (foreground)
- ❌ Notification tidak muncul di browser (background)

---

## 🧪 Testing Steps

### Step 1: Test Manual Notification

**Purpose:** Verify browser can show notifications at all

**Action:**
1. Open: `http://localhost:8000/test-notification-display.html`
2. Click: **"Test 1: Manual Browser Notification"**
3. **Expected:** Notification muncul di browser

**If notification appears:**
✅ Browser notification system is working
→ Problem is with FCM message handling

**If notification does NOT appear:**
❌ Browser notification system has issues
→ Check:
- Windows notification settings
- Browser notification settings
- Do Not Disturb mode

---

### Step 2: Test FCM Foreground Simulation

**Purpose:** Test notification rendering with FCM-like payload

**Action:**
1. Stay on test page (keep it visible)
2. Click: **"Test 2: Simulate FCM Foreground Message"**
3. **Expected:** Notification muncul

**If notification appears:**
✅ Notification rendering code is working
→ Problem is with FCM message delivery

**If notification does NOT appear:**
❌ Notification rendering code has issues
→ Check console for errors

---

### Step 3: Test FCM Background Simulation

**Purpose:** Test service worker notification

**Action:**
1. Stay on test page
2. Click: **"Test 3: Simulate FCM Background Message"**
3. **Expected:** Notification muncul via service worker

**If notification appears:**
✅ Service worker notification is working
→ Problem is with FCM background message handler

**If notification does NOT appear:**
❌ Service worker has issues
→ Check service worker console

---

### Step 4: Test Real FCM Message

**Purpose:** Test actual FCM message from backend

**Action:**
1. Run: `php test-send-notification.php`
2. Enter user ID
3. **Expected:** Notification sent and appears

**Check:**
- Laravel log: `tail -f storage/logs/laravel.log | grep FCM`
- Browser console: Look for "FOREGROUND MESSAGE RECEIVED"
- Service Worker console: Look for "BACKGROUND MESSAGE RECEIVED"

---

## 🔍 Debug Checklist

### Browser Console Checks

```javascript
// 1. Check permission
console.log('Permission:', Notification.permission);
// Expected: "granted"

// 2. Test manual notification
new Notification('Test', { body: 'Test message', icon: '/icons/logo-arradea.png' });
// Expected: Notification appears

// 3. Check service worker
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('SW registrations:', regs.length);
    regs.forEach(reg => console.log('  Scope:', reg.scope));
});
// Expected: At least 1 registration

// 4. Check if page is controlled
console.log('Controlled by SW:', !!navigator.serviceWorker.controller);
// Expected: true

// 5. Check visibility
console.log('Page visibility:', document.visibilityState);
// Expected: "visible" for foreground, "hidden" for background
```

### Service Worker Console Checks

**How to access:**
1. Open DevTools (F12)
2. Go to: Application → Service Workers
3. Click on "firebase-messaging-sw.js"
4. Check console

**Expected logs when message arrives:**
```
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: [title]
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Manual notification works, FCM doesn't

**Diagnosis:**
- Browser notification system: ✅ Working
- FCM message handling: ❌ Not working

**Check:**
1. Is `onMessage()` handler registered?
   ```javascript
   // In browser console:
   console.log('FCM initialized:', !!window.Arradea?.notification);
   ```

2. Is foreground handler setup?
   ```javascript
   // Look for in console:
   "✅ Foreground message handler setup complete"
   ```

3. When backend sends, check console for:
   ```
   📬 FOREGROUND MESSAGE RECEIVED
   ```

**Solution:**
- If no "FOREGROUND MESSAGE RECEIVED" → FCM not receiving messages
- Check Firebase config matches between frontend and backend
- Check FCM token is correct

---

### Issue 2: Foreground works, background doesn't

**Diagnosis:**
- Foreground: ✅ Working
- Background: ❌ Not working

**Check:**
1. Service worker registered?
   ```javascript
   navigator.serviceWorker.getRegistrations()
   ```

2. Service worker active?
   ```javascript
   navigator.serviceWorker.controller
   ```

3. Background handler exists in SW?
   - Check `public/firebase-messaging-sw.js`
   - Look for `messaging.onBackgroundMessage()`

**Solution:**
- Hard refresh: Ctrl+Shift+R
- Unregister and re-register SW
- Check SW console for errors

---

### Issue 3: Nothing works

**Diagnosis:**
- Everything broken

**Check:**
1. Permission granted?
   ```javascript
   Notification.permission === 'granted'
   ```

2. Windows notification settings enabled?
   - Settings → System → Notifications
   - Find your browser
   - Ensure enabled

3. Browser notification settings enabled?
   - Click 🔒 in address bar
   - Site settings → Notifications → Allow

4. Do Not Disturb mode off?
   - Windows notification center
   - Check DND is off

**Solution:**
- Reset all permissions
- Restart browser
- Test with `test-notification-display.html`

---

## 📊 Expected Behavior

### Foreground (App Open & Visible)

**When message arrives:**
1. `onMessage()` handler triggered
2. Console log: "📬 FOREGROUND MESSAGE RECEIVED"
3. Notification created with `new Notification()`
4. Notification appears in browser
5. Click notification → opens URL

### Background (App Minimized or Hidden)

**When message arrives:**
1. Service worker receives message
2. Console log: "[SW] 📬 BACKGROUND MESSAGE RECEIVED"
3. `self.registration.showNotification()` called
4. Notification appears in Windows notification center
5. Click notification → opens/focuses browser

---

## 🧪 Test Commands

### Test from Browser Console

```javascript
// Test 1: Manual notification
new Notification('Test 🔥', { 
    body: 'Manual test', 
    icon: '/icons/logo-arradea.png' 
});

// Test 2: Check FCM
console.log('FCM available:', !!window.Arradea?.notification);
console.log('Permission:', Notification.permission);

// Test 3: Check SW
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('SW count:', regs.length);
    regs.forEach(reg => console.log('Scope:', reg.scope));
});

// Test 4: Service worker notification
navigator.serviceWorker.ready.then(reg => {
    reg.showNotification('SW Test', {
        body: 'Test from service worker',
        icon: '/icons/logo-arradea.png'
    });
});
```

### Test from Backend

```bash
# Test send notification
php test-send-notification.php

# Watch logs
tail -f storage/logs/laravel.log | grep FCM

# Check tokens
php check-fcm-tokens.php
```

---

## 📝 Debug Log Analysis

### Good Foreground Log

```
[FCM] Initializing Firebase Cloud Messaging...
✅ [FCM] Firebase Cloud Messaging initialized successfully!
📬 Setting up foreground message handler...
✅ Foreground message handler setup complete
================================================================================
📬 FOREGROUND MESSAGE RECEIVED
================================================================================
Full payload: {
  "notification": {
    "title": "🛒 Pesanan Baru!",
    "body": "Pesanan baru dari..."
  }
}
🔔 Creating browser notification...
✅ Browser notification created successfully
```

### Good Background Log (Service Worker)

```
[firebase-messaging-sw.js] Service worker activated
================================================================================
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
================================================================================
Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: 🛒 Pesanan Baru!
```

### Bad Log (No message received)

```
[FCM] Initializing Firebase Cloud Messaging...
✅ [FCM] Firebase Cloud Messaging initialized successfully!
📬 Setting up foreground message handler...
✅ Foreground message handler setup complete
(no further logs when message sent)
```

**Problem:** FCM not receiving messages
**Check:** Token, Firebase config, backend payload

---

## 🎯 Success Criteria

System is working when:

1. ✅ Manual notification appears
2. ✅ FCM foreground simulation appears
3. ✅ FCM background simulation appears
4. ✅ Real FCM message appears (foreground)
5. ✅ Real FCM message appears (background)
6. ✅ Console shows "FOREGROUND MESSAGE RECEIVED"
7. ✅ SW console shows "BACKGROUND MESSAGE RECEIVED"
8. ✅ Click notification opens correct URL

---

## 🔗 Test URLs

- **Display Test:** `http://localhost:8000/test-notification-display.html`
- **System Test:** `http://localhost:8000/test-notification.html`
- **Main App:** `http://localhost:8000`

---

## 📞 Next Steps

1. **Open:** `http://localhost:8000/test-notification-display.html`
2. **Run all 3 tests** in order
3. **Note which tests pass/fail**
4. **Share results** for further debugging

---

**Status:** 🔍 **DEBUGGING IN PROGRESS**
**Focus:** Notification display/rendering
**Goal:** Make notifications appear!
