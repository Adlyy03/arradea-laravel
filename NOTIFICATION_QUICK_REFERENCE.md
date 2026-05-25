# 🔔 Notification Quick Reference

## 📋 All Notification Events

| # | Event | Recipient | Title | Trigger |
|---|-------|-----------|-------|---------|
| 1 | New Order | Seller | 🛒 Pesanan Baru! | Order created |
| 2 | Order Status | Buyer | 📦 Status Pesanan Diperbarui | Status changed |
| 3 | Payment Submitted | Seller | 💳 Bukti Pembayaran Diterima | Proof uploaded |
| 4 | Payment Approved | Buyer | ✅ Pembayaran Dikonfirmasi! | Payment approved |
| 5 | Payment Rejected | Buyer | ❌ Pembayaran Ditolak | Payment rejected |
| 6 | Payment Resubmitted | Seller | 🔄 Bukti Pembayaran Diupload Ulang | Proof re-uploaded |
| 7 | Chat Message | Both | 💬 Pesan dari {name} | Message sent |

---

## 🚀 Quick Implementation

### Basic Usage
```php
use App\Services\PushNotificationService;

class YourController extends Controller
{
    protected $pushNotification;

    public function __construct(PushNotificationService $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    public function yourMethod()
    {
        $this->pushNotification->sendToUser(
            $user,              // User model
            'Title',            // Notification title
            'Body message',     // Notification body
            ['key' => 'value'], // Data payload (optional)
            asset('icon.png'),  // Icon URL (optional)
            url('/target')      // Click action URL (optional)
        );
    }
}
```

---

## 📝 Code Snippets

### 1. New Order (Seller)
```php
$this->pushNotification->sendToUser(
    $seller,
    '🛒 Pesanan Baru!',
    "Pesanan baru dari {$buyer->name} senilai Rp " . number_format($order->total_price, 0, ',', '.'),
    [
        'type' => 'new_order',
        'order_id' => $order->id,
    ],
    asset('icons/logo-arradea.png'),
    url('/seller/orders')
);
```

### 2. Order Status Update (Buyer)
```php
$statusText = [
    'processing' => 'sedang diproses',
    'shipped' => 'sedang dikirim',
    'completed' => 'telah selesai',
    'cancelled' => 'dibatalkan',
][$status];

$this->pushNotification->sendToUser(
    $buyer,
    '📦 Status Pesanan Diperbarui',
    "Pesanan #{$order->id} Anda {$statusText}",
    [
        'type' => 'order_status',
        'order_id' => $order->id,
        'status' => $status,
    ],
    asset('icons/logo-arradea.png'),
    url('/buyer/orders/' . $order->id)
);
```

### 3. Payment Approved (Buyer)
```php
$this->pushNotification->sendToUser(
    $buyer,
    '✅ Pembayaran Dikonfirmasi!',
    "Pembayaran Anda untuk pesanan #{$order->id} telah dikonfirmasi. Pesanan sedang diproses.",
    [
        'type' => 'payment_approved',
        'order_id' => $order->id,
    ],
    asset('icons/logo-arradea.png'),
    url('/buyer/orders/' . $order->id)
);
```

### 4. Chat Message
```php
$messagePreview = strlen($message) > 50 
    ? substr($message, 0, 50) . '...' 
    : $message;

$this->pushNotification->sendToUser(
    $recipient,
    "💬 Pesan dari {$sender->name}",
    $messagePreview,
    [
        'type' => 'chat_message',
        'chat_id' => $chat->id,
        'order_id' => $order->id,
        'sender_id' => $sender->id,
    ],
    asset('icons/logo-arradea.png'),
    url('/chat/' . $order->id)
);
```

---

## 🧪 Testing Commands

### Test via Tinker
```bash
php artisan tinker
```

```php
// Get service
$service = app(App\Services\PushNotificationService::class);

// Get user
$user = App\Models\User::find(1);

// Send test notification
$service->sendToUser(
    $user,
    '🧪 Test Notification',
    'This is a test message',
    ['type' => 'test'],
    asset('icons/logo-arradea.png'),
    url('/')
);
```

### Check Logs
```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log | grep "FCM"

# Check specific notification
tail -f storage/logs/laravel.log | grep "SENDING FCM"
```

---

## 🔍 Debugging

### Browser Console
```javascript
// Check permission
Notification.permission

// Check service worker
window.debugServiceWorkers()

// Test notification
new Notification('Test', { 
    body: 'Test message', 
    icon: '/icons/logo-arradea.png' 
})
```

### Check FCM Tokens
```bash
php artisan tinker
```

```php
// Count active tokens
App\Models\FcmToken::where('is_active', true)->count()

// Get user tokens
App\Models\User::find(1)->fcmTokens()->active()->get()

// Check specific user
$user = App\Models\User::find(1);
$user->fcmTokens()->active()->pluck('token')
```

---

## 📊 Response Handling

### Success Response
```php
[
    'success' => true,
    'total' => 1,
    'successful' => 1,
    'failed' => 0,
    'invalid_tokens' => []
]
```

### Error Response
```php
[
    'success' => false,
    'message' => 'Error message here'
]
```

### Check Response
```php
$result = $this->pushNotification->sendToUser(...);

if ($result['success']) {
    \Log::info("Notification sent to {$result['successful']} users");
} else {
    \Log::error("Notification failed: {$result['message']}");
}
```

---

## 🎯 Best Practices

### ✅ DO
- Keep titles under 50 characters
- Keep body under 100 characters
- Use emojis for visual appeal
- Include relevant data payload
- Set proper click actions
- Handle errors gracefully
- Test in foreground and background

### ❌ DON'T
- Don't send too many notifications
- Don't include sensitive data in body
- Don't break app flow if notification fails
- Don't forget to log errors
- Don't use long messages

---

## 🔗 File Locations

| File | Purpose |
|------|---------|
| `app/Services/PushNotificationService.php` | Main service |
| `app/Http/Controllers/OrderController.php` | Order notifications |
| `app/Http/Controllers/PaymentWebController.php` | Payment notifications |
| `app/Http/Controllers/ChatController.php` | Chat notifications |
| `resources/js/firebase.js` | Frontend handler |
| `public/firebase-messaging-sw.js` | Service worker |

---

## 📚 Documentation

- **Complete Guide:** `FCM_NOTIFICATION_EVENTS.md`
- **Setup Guide:** `README_FCM_FIXES.md`
- **Testing:** `FCM_QUICK_TEST.md`
- **Debugging:** `FCM_DEBUG_GUIDE.md`

---

## ⚡ Quick Commands

```bash
# Build assets
npm run build

# Clear cache
php artisan cache:clear

# Test notification
php artisan tinker
>>> app(App\Services\PushNotificationService::class)->sendToUser(...)

# Watch logs
tail -f storage/logs/laravel.log | grep FCM

# Check tokens
php artisan tinker
>>> App\Models\FcmToken::where('is_active', true)->count()
```

---

**Version:** 1.0  
**Last Updated:** 2026-05-22
