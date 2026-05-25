# 🔧 FCM Notification Fix Guide

## Problem Summary

**Root Cause:** Wrong Service Worker is registered (`sw.js` instead of `firebase-messaging-sw.js`)

**Impact:** 
- ✅ FCM token generation works
- ✅ Backend sends notifications successfully
- ✅ Foreground notifications work
- ❌ **Background notifications DON'T work** (because wrong SW is registered)

## Test Results Analysis

From your test results:
```
Test 1: ❌ FAILED - Shows sw.js is registered (WRONG!)
Test 2: ✅ PASSED - SW showNotification works
Test 3: ✅ PASSED - SW showNotification with options works
Test 4: ✅ PASSED - Permissions are granted
```

**Conclusion:** The Service Worker CAN show notifications, but it's the WRONG service worker. It doesn't have Firebase messaging handlers, so FCM messages are never received.

## Solution: Fix Service Worker Registration

### Option 1: One-Click Fix (RECOMMENDED)

1. **Open the fix page:**
   ```
   http://localhost:8000/fix-sw-now.html
   ```

2. **Click "FIX NOW" button**
   - Automatically unregisters `sw.js`
   - Registers `firebase-messaging-sw.js`
   - Verifies the fix
   - Reloads the page

3. **Done!** The correct service worker is now registered.

### Option 2: Manual Fix

If you prefer to do it manually:

1. **Open DevTools Console** (F12)

2. **Unregister old service worker:**
   ```javascript
   navigator.serviceWorker.getRegistrations().then(registrations => {
       registrations.forEach(reg => reg.unregister());
       console.log('All SW unregistered');
   });
   ```

3. **Reload the page** (Ctrl+R)

4. **Register Firebase SW:**
   ```javascript
   navigator.serviceWorker.register('/firebase-messaging-sw.js', {
       scope: '/',
       updateViaCache: 'none'
   }).then(reg => {
       console.log('Firebase SW registered:', reg);
   });
   ```

5. **Verify:**
   ```javascript
   navigator.serviceWorker.getRegistrations().then(registrations => {
       registrations.forEach(reg => {
           console.log('Active SW:', reg.active?.scriptURL);
       });
   });
   ```

   Should show: `http://localhost:8000/firebase-messaging-sw.js`

## After Fixing Service Worker

### Step 1: Login to Main App

**Important:** Test pages don't save FCM tokens to database because they don't have Laravel session.

1. Open: `http://localhost:8000/login`
2. Login with your account
3. Allow notification permission when prompted
4. Check console for FCM token generation logs

### Step 2: Verify Token in Database

Run this command:
```bash
php check-fcm-tokens.php
```

You should see your FCM token in the database.

### Step 3: Test FCM Notification

Run the test script:
```bash
php test-send-notification.php
```

**Expected behavior:**
- ✅ Backend logs show "Notification sent successfully"
- ✅ Notification appears in browser (even if tab is in background)
- ✅ Notification appears in Windows notification center

### Step 4: Test Real Scenarios

1. **Test Order Notification:**
   - Login as buyer
   - Create an order
   - Seller should receive notification

2. **Test Chat Notification:**
   - Login as user A
   - Send message to user B
   - User B should receive notification

3. **Test Payment Notification:**
   - Submit payment proof
   - Seller should receive notification

## Debugging Tips

### Check Service Worker Console

1. Open DevTools (F12)
2. Go to **Application** tab
3. Click **Service Workers** in left sidebar
4. Click **"firebase-messaging-sw.js"** link
5. Check console logs in the new window

You should see:
```
[firebase-messaging-sw.js] 🚀 Service Worker loading...
[firebase-messaging-sw.js] ✅ Firebase initialized
[firebase-messaging-sw.js] ✅ Service Worker ready
```

When notification arrives:
```
[firebase-messaging-sw.js] 📬 BACKGROUND MESSAGE RECEIVED
[firebase-messaging-sw.js] Full payload: {...}
[firebase-messaging-sw.js] 🔔 Showing notification: New Order
```

### Check Main Console

In the main page console, you should see:
```
[FCM] 🚀 Initializing Firebase...
[FCM] ✅ Firebase initialized
[FCM] 📝 Requesting notification permission...
[FCM] ✅ Notification permission granted
[FCM] 🔑 Getting FCM token...
[FCM] ✅ FCM Token: eyJhbGc...
[FCM] 💾 Saving token to backend...
[FCM] ✅ Token saved successfully
```

### Common Issues

#### Issue 1: "sw.js" still registered after fix

**Solution:**
1. Open DevTools → Application → Service Workers
2. Click "Unregister" next to sw.js
3. Reload page
4. Run fix again

#### Issue 2: Token not in database

**Cause:** You're testing on standalone test pages without Laravel session.

**Solution:** Login to main app at `http://localhost:8000/login`

#### Issue 3: Notification permission denied

**Solution:**
1. Click the lock icon in address bar
2. Click "Site settings"
3. Find "Notifications"
4. Change to "Allow"
5. Reload page

#### Issue 4: Notifications work in foreground but not background

**Cause:** Service worker not registered or wrong SW registered.

**Solution:** Run the fix tool again.

## File Structure

```
public/
├── firebase-messaging-sw.js    ← CORRECT SW (Firebase + PWA)
├── sw.js                       ← OLD SW (PWA only, no Firebase)
├── fix-sw-now.html            ← One-click fix tool
├── test-notification-display.html  ← Display tests
└── test-sw-notification.html   ← SW tests

resources/js/
├── firebase.js                 ← Foreground handler
└── app.js                      ← FCM initialization

app/Services/
└── PushNotificationService.php ← Backend notification sender
```

## What Changed

### firebase-messaging-sw.js (Updated)

Now includes BOTH:
- ✅ Firebase messaging handlers (for FCM notifications)
- ✅ PWA caching (for offline support)

This means you only need ONE service worker for everything.

### Why This Fixes the Problem

**Before:**
- `sw.js` was registered (PWA caching only)
- No Firebase handlers → FCM messages ignored
- Background notifications didn't work

**After:**
- `firebase-messaging-sw.js` is registered
- Has Firebase handlers → FCM messages received
- Has PWA caching → Offline support maintained
- Background notifications work ✅

## Next Steps

1. ✅ Fix service worker (use fix-sw-now.html)
2. ✅ Login to main app
3. ✅ Verify token in database
4. ✅ Test with php test-send-notification.php
5. ✅ Test real scenarios (orders, chat, payments)

## Success Criteria

When everything works correctly:

- ✅ `firebase-messaging-sw.js` is registered (not `sw.js`)
- ✅ FCM token is in database
- ✅ Foreground notifications appear
- ✅ Background notifications appear
- ✅ Notifications appear in Windows notification center
- ✅ Console shows detailed logs
- ✅ Service worker console shows message received logs

## Support

If you still have issues after following this guide:

1. Check browser console for errors
2. Check service worker console for errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify Firebase config matches in both files
5. Try in incognito mode (fresh start)

---

**Last Updated:** May 25, 2026
**Status:** Ready to fix
