# 🏗️ FCM Architecture & Flow

## Current Problem Visualization

```
┌─────────────────────────────────────────────────────────────┐
│                     CURRENT STATE (BROKEN)                   │
└─────────────────────────────────────────────────────────────┘

Firebase Cloud Messaging Server
        │
        │ Sends notification
        ▼
┌──────────────────────┐
│   Browser (Chrome)   │
│                      │
│  ┌────────────────┐  │
│  │   sw.js        │  │  ❌ NO Firebase handlers
│  │   (PWA only)   │  │  ❌ Message IGNORED
│  └────────────────┘  │
│                      │
│  ┌────────────────┐  │
│  │ firebase-      │  │  ✅ Has Firebase handlers
│  │ messaging-sw.js│  │  ⚠️ But NOT registered!
│  │ (NOT ACTIVE)   │  │
│  └────────────────┘  │
└──────────────────────┘

Result: ❌ No notification displayed
```

## After Fix (Working)

```
┌─────────────────────────────────────────────────────────────┐
│                     AFTER FIX (WORKING)                      │
└─────────────────────────────────────────────────────────────┘

Firebase Cloud Messaging Server
        │
        │ Sends notification
        ▼
┌──────────────────────────────────────────────────────────────┐
│   Browser (Chrome)                                           │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │   firebase-messaging-sw.js (ACTIVE)                    │ │
│  │                                                        │ │
│  │   ✅ Firebase onBackgroundMessage() handler           │ │
│  │   ✅ PWA caching                                       │ │
│  │   ✅ Notification display                              │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │   Main Page (resources/js/firebase.js)                │ │
│  │                                                        │ │
│  │   ✅ Foreground onMessage() handler                    │ │
│  │   ✅ Token generation                                  │ │
│  │   ✅ Permission request                                │ │
│  └────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

Result: ✅ Notification displayed in browser & Windows
```

## Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                        COMPLETE FCM FLOW                            │
└─────────────────────────────────────────────────────────────────────┘

1. USER OPENS APP
   │
   ├─→ resources/js/app.js
   │   └─→ Imports firebase.js
   │
   └─→ resources/js/firebase.js
       ├─→ Initialize Firebase
       ├─→ Request notification permission
       ├─→ Register service worker (firebase-messaging-sw.js)
       ├─→ Get FCM token
       ├─→ Save token to backend (Laravel)
       └─→ Setup foreground message handler

2. BACKEND SENDS NOTIFICATION
   │
   ├─→ app/Services/PushNotificationService.php
   │   ├─→ Get user's FCM token from database
   │   ├─→ Build notification payload
   │   └─→ Send to Firebase Cloud Messaging
   │
   └─→ Firebase Cloud Messaging Server
       └─→ Delivers to browser

3. BROWSER RECEIVES NOTIFICATION
   │
   ├─→ IF TAB IS ACTIVE (Foreground)
   │   │
   │   └─→ resources/js/firebase.js
   │       └─→ onMessage() handler
   │           ├─→ Show browser notification
   │           ├─→ Show Arradea toast
   │           └─→ Log to console
   │
   └─→ IF TAB IS INACTIVE (Background)
       │
       └─→ public/firebase-messaging-sw.js
           └─→ onBackgroundMessage() handler
               ├─→ Show notification via SW
               ├─→ Log to SW console
               └─→ Handle notification click

4. USER CLICKS NOTIFICATION
   │
   └─→ public/firebase-messaging-sw.js
       └─→ notificationclick event
           ├─→ Close notification
           ├─→ Get URL from notification data
           └─→ Open/focus window with URL
```

## File Responsibilities

```
┌─────────────────────────────────────────────────────────────┐
│                    FILE RESPONSIBILITIES                     │
└─────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ FRONTEND (Browser)                                           │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│ resources/js/firebase.js                                     │
│ ├─ Initialize Firebase                                       │
│ ├─ Request notification permission                           │
│ ├─ Get FCM token                                             │
│ ├─ Save token to backend                                     │
│ ├─ Setup foreground message handler                          │
│ └─ Register service worker                                   │
│                                                              │
│ resources/js/app.js                                          │
│ ├─ Import firebase.js                                        │
│ ├─ Call requestPermission()                                  │
│ └─ Call setupForegroundMessageHandler()                      │
│                                                              │
│ public/firebase-messaging-sw.js (Service Worker)             │
│ ├─ Import Firebase SDK                                       │
│ ├─ Initialize Firebase                                       │
│ ├─ Handle background messages (onBackgroundMessage)          │
│ ├─ Show notifications                                        │
│ ├─ Handle notification clicks                                │
│ └─ PWA caching (offline support)                             │
│                                                              │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ BACKEND (Laravel)                                            │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│ app/Services/PushNotificationService.php                     │
│ ├─ Send notification to single user                          │
│ ├─ Send notification to multiple users                       │
│ ├─ Build notification payload                                │
│ └─ Call Firebase Cloud Messaging API                         │
│                                                              │
│ app/Http/Controllers/*Controller.php                         │
│ ├─ OrderController: New order → Seller                       │
│ ├─ OrderController: Status update → Buyer                    │
│ ├─ PaymentWebController: Payment events → Seller/Buyer       │
│ └─ ChatController: New message → Recipient                   │
│                                                              │
│ app/Models/FcmToken.php                                      │
│ ├─ Store FCM tokens                                          │
│ ├─ Associate with users                                      │
│ └─ Query tokens for notification sending                     │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## Notification Types & Triggers

```
┌─────────────────────────────────────────────────────────────┐
│                    NOTIFICATION TRIGGERS                     │
└─────────────────────────────────────────────────────────────┘

1. ORDER NOTIFICATIONS
   ├─ New Order Created
   │  ├─ Trigger: OrderController::store()
   │  ├─ Recipient: Seller
   │  └─ Message: "Pesanan baru dari {buyer}"
   │
   └─ Order Status Updated
      ├─ Trigger: OrderController::updateStatus()
      ├─ Recipient: Buyer
      └─ Message: "Status pesanan: {status}"

2. PAYMENT NOTIFICATIONS
   ├─ Payment Proof Submitted
   │  ├─ Trigger: PaymentWebController::uploadProof()
   │  ├─ Recipient: Seller
   │  └─ Message: "Bukti pembayaran diterima"
   │
   ├─ Payment Approved
   │  ├─ Trigger: PaymentWebController::approve()
   │  ├─ Recipient: Buyer
   │  └─ Message: "Pembayaran disetujui"
   │
   ├─ Payment Rejected
   │  ├─ Trigger: PaymentWebController::reject()
   │  ├─ Recipient: Buyer
   │  └─ Message: "Pembayaran ditolak"
   │
   └─ Payment Resubmitted
      ├─ Trigger: PaymentWebController::reuploadProof()
      ├─ Recipient: Seller
      └─ Message: "Bukti pembayaran baru"

3. CHAT NOTIFICATIONS
   └─ New Message
      ├─ Trigger: ChatController::store()
      ├─ Recipient: Other user in chat
      └─ Message: "{sender}: {message}"
```

## Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│                        DATA FLOW                             │
└─────────────────────────────────────────────────────────────┘

TOKEN REGISTRATION FLOW:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Browser │───▶│ Firebase │───▶│  Laravel │───▶│ Database │
│          │    │   SDK    │    │ Backend  │    │          │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
   Request         Generate        Save           Store
   Token           Token           Token          Token

NOTIFICATION SEND FLOW:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Event   │───▶│  Laravel │───▶│ Firebase │───▶│  Browser │
│ Trigger  │    │ Backend  │    │   FCM    │    │          │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
   Order          Get Token       Send to         Display
   Created        from DB         Device          Notification
```

## Service Worker States

```
┌─────────────────────────────────────────────────────────────┐
│                  SERVICE WORKER LIFECYCLE                    │
└─────────────────────────────────────────────────────────────┘

1. INSTALLING
   ├─ Service worker script is being installed
   ├─ Cache is being populated
   └─ Event: 'install'

2. INSTALLED (Waiting)
   ├─ Installation complete
   ├─ Waiting to activate
   └─ Can call skipWaiting() to activate immediately

3. ACTIVATING
   ├─ Service worker is activating
   ├─ Old caches are being cleaned up
   └─ Event: 'activate'

4. ACTIVATED
   ├─ Service worker is active and running
   ├─ Can handle fetch events
   ├─ Can receive push notifications
   └─ Controls pages in scope

5. REDUNDANT
   ├─ Service worker has been replaced
   └─ No longer active
```

## Debugging Checklist

```
┌─────────────────────────────────────────────────────────────┐
│                    DEBUGGING CHECKLIST                       │
└─────────────────────────────────────────────────────────────┘

✓ Check which service worker is registered
  → navigator.serviceWorker.getRegistrations()
  → Should be: firebase-messaging-sw.js
  → NOT: sw.js

✓ Check notification permission
  → Notification.permission
  → Should be: "granted"

✓ Check FCM token exists
  → Check console logs
  → Check database: php check-fcm-tokens.php

✓ Check service worker console
  → DevTools → Application → Service Workers
  → Click service worker link
  → Check console in new window

✓ Check main page console
  → Should see [FCM] logs
  → Should see token generation
  → Should see token save success

✓ Check backend logs
  → storage/logs/laravel.log
  → Should see notification send logs

✓ Test manual notification
  → new Notification('Test', {body: 'Test'})
  → Should appear immediately

✓ Test FCM notification
  → php test-send-notification.php
  → Should appear in browser
```

---

**This architecture ensures:**
- ✅ Notifications work in foreground (tab active)
- ✅ Notifications work in background (tab inactive)
- ✅ Notifications appear in Windows notification center
- ✅ PWA offline support maintained
- ✅ Comprehensive logging for debugging
- ✅ Clean separation of concerns
