# 🔧 Quick Fix: Token Not Saving

## 🔍 Problem Identified

**From logs:**
- ✅ FCM token generated successfully
- ✅ Manual notification works
- ❌ Token NOT saved to database

**Token from test page:**
```
ek6fcqBsc-zx3AdIXygFvn:APA91bHBiXfFv504hbhh98m12ENShZ1WRCuYamnnubctDAmMhoLsgAxb8_O-hpjse90qjra8dEysXKdwXkdVY8MZrFvTdX-_Lf1cRqxN3toTXiFH97bnTco
```

## ⚠️ Issue

Test page (`test-notification.html`) generates token but **doesn't save to backend** because:
1. Test page is standalone (no Laravel session)
2. No CSRF token
3. No user authentication

## ✅ Solution

**You need to login to the MAIN APP** (not test page) to save token.

### Step 1: Login to Main App

1. **Close test page**
2. **Open:** `http://localhost:8000/login`
3. **Login** with your account (Seller: Reza or Buyer: Bayu)
4. **Wait for auto-request** (5 seconds after page load)

### Step 2: Check Console

After login, check browser console for:
```
================================================================================
🔔 [FCM] Requesting notification permission...
================================================================================
📋 [FCM] Permission result: granted
🔑 [FCM] Getting FCM token...
================================================================================
🔑 [FCM] FCM Token obtained successfully!
================================================================================
💾 [FCM] Saving FCM token to backend...
================================================================================
✅ [FCM] FCM token berhasil disimpan ke backend!
================================================================================
```

### Step 3: Verify Token Saved

```bash
php check-fcm-tokens.php
```

Expected output:
```
Total users with FCM tokens: 1

User: Reza (ID: 25)
  Role: Seller
  Active tokens: 1
```

---

## 🧪 Alternative: Manual Token Save

If auto-save doesn't work, manually save via console:

### Step 1: Login to Main App

Login at: `http://localhost:8000/login`

### Step 2: Open Console (F12)

### Step 3: Run This Command

```javascript
// Request permission and save token
window.Arradea?.notification?.request().then(token => {
    if (token) {
        console.log('✅ Token saved:', token);
    } else {
        console.log('❌ Failed to get token');
    }
});
```

### Step 4: Verify

```bash
php check-fcm-tokens.php
```

---

## 🎯 After Token is Saved

Once token is saved, test notification:

### Test 1: From Backend Script

```bash
php test-send-notification.php
```

Enter user ID and check if notification appears.

### Test 2: Create Order

1. Login as **Buyer**
2. Create new order
3. **Seller should receive notification** (if seller has token)

### Test 3: Send Chat Message

1. Open chat
2. Send message
3. **Recipient should receive notification**

---

## 📋 Checklist

- [ ] Login to main app (not test page)
- [ ] Allow notification permission
- [ ] Wait for "FCM token berhasil disimpan"
- [ ] Verify with: `php check-fcm-tokens.php`
- [ ] Test with: `php test-send-notification.php`
- [ ] Check notification appears

---

## 🔍 Debug: Why Token Not Saving

### Check 1: CSRF Token

```javascript
// In browser console:
document.querySelector('meta[name="csrf-token"]')?.content
// Should return a token string
```

### Check 2: User Authenticated

```javascript
// In browser console:
document.querySelector('meta[name="user-authenticated"]')?.content
// Should return "true"
```

### Check 3: Network Request

1. Open DevTools → Network tab
2. Request permission
3. Look for POST to `/save-fcm-token`
4. Check response status (should be 200)

### Check 4: Laravel Log

```bash
tail -f storage/logs/laravel.log | grep "FCM token saved"
```

---

## ✅ Success Criteria

System is ready when:

1. ✅ User logged in to main app
2. ✅ Permission granted
3. ✅ Token saved to database
4. ✅ `php check-fcm-tokens.php` shows token
5. ✅ `php test-send-notification.php` sends successfully
6. ✅ Notification appears in browser

---

## 🚀 Quick Commands

```bash
# Check tokens
php check-fcm-tokens.php

# Test send
php test-send-notification.php

# Watch logs
tail -f storage/logs/laravel.log | grep FCM
```

---

**Status:** ⚠️ **ACTION REQUIRED**

**Next Step:** Login to main app and allow notifications!
