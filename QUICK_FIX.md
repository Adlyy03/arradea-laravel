# 🚀 Quick Fix - FCM Notifications

## Problem
Wrong service worker registered: `sw.js` instead of `firebase-messaging-sw.js`

## Solution (30 seconds)

### Step 1: Open Fix Page
```
http://localhost:8000/fix-sw-now.html
```

### Step 2: Click "FIX NOW" Button
- Unregisters old SW
- Registers Firebase SW
- Auto-reloads page

### Step 3: Login to Main App
```
http://localhost:8000/login
```
Allow notifications when prompted.

### Step 4: Test
```bash
php test-send-notification.php
```

## Done! ✅

Notifications should now appear in:
- ✅ Browser (foreground)
- ✅ Browser (background)
- ✅ Windows notification center

---

**Need more details?** Read `FCM_FIX_GUIDE.md`
