# 🔔 FCM Notification Events - Complete Guide

## 📋 Overview

Sistem notifikasi push telah diimplementasikan untuk semua event penting di marketplace Arradea. Berikut adalah daftar lengkap semua notifikasi yang akan dikirim secara otomatis.

---

## 🛒 Order Notifications

### 1. New Order (Seller)
**Trigger:** Saat buyer membuat pesanan baru

**Recipient:** Seller (pemilik toko)

**Notification:**
- **Title:** `🛒 Pesanan Baru!`
- **Body:** `Pesanan baru dari {buyer_name} senilai Rp {total_price}`
- **Data:**
  - `type`: `new_order`
  - `order_id`: ID pesanan
- **Click Action:** `/seller/orders`

**Code Location:** `app/Http/Controllers/OrderController.php` → `store()`

---

### 2. Order Status Updated (Buyer)
**Trigger:** Saat seller mengubah status pesanan

**Recipient:** Buyer (pembeli)

**Notification:**
- **Title:** `📦 Status Pesanan Diperbarui`
- **Body:** `Pesanan #{order_id} Anda {status_text}`
- **Status Text:**
  - `processing` → "sedang diproses"
  - `shipped` → "sedang dikirim"
  - `completed` → "telah selesai"
  - `cancelled` → "dibatalkan"
- **Data:**
  - `type`: `order_status`
  - `order_id`: ID pesanan
  - `status`: Status baru
- **Click Action:** `/buyer/orders/{order_id}`

**Code Location:** `app/Http/Controllers/OrderController.php` → `updateStatus()`

---

## 💳 Payment Notifications

### 3. Payment Proof Submitted (Seller)
**Trigger:** Saat buyer upload bukti pembayaran QRIS

**Recipient:** Seller (pemilik toko)

**Notification:**
- **Title:** `💳 Bukti Pembayaran Diterima`
- **Body:** `Pembeli {buyer_name} telah mengunggah bukti pembayaran untuk pesanan #{order_id}`
- **Data:**
  - `type`: `payment_submitted`
  - `order_id`: ID pesanan
- **Click Action:** `/seller/payments`

**Code Location:** `app/Http/Controllers/PaymentWebController.php` → `uploadProof()`

---

### 4. Payment Approved (Buyer)
**Trigger:** Saat seller menyetujui bukti pembayaran

**Recipient:** Buyer (pembeli)

**Notification:**
- **Title:** `✅ Pembayaran Dikonfirmasi!`
- **Body:** `Pembayaran Anda untuk pesanan #{order_id} telah dikonfirmasi. Pesanan sedang diproses.`
- **Data:**
  - `type`: `payment_approved`
  - `order_id`: ID pesanan
- **Click Action:** `/buyer/orders/{order_id}`

**Code Location:** `app/Http/Controllers/PaymentWebController.php` → `approve()`

---

### 5. Payment Rejected (Buyer)
**Trigger:** Saat seller menolak bukti pembayaran

**Recipient:** Buyer (pembeli)

**Notification:**
- **Title:** `❌ Pembayaran Ditolak`
- **Body:** `Bukti pembayaran untuk pesanan #{order_id} ditolak. {reason}`
- **Data:**
  - `type`: `payment_rejected`
  - `order_id`: ID pesanan
  - `reason`: Alasan penolakan (optional)
- **Click Action:** `/buyer/payments`

**Code Location:** `app/Http/Controllers/PaymentWebController.php` → `reject()`

---

### 6. Payment Resubmitted (Seller)
**Trigger:** Saat buyer upload ulang bukti pembayaran setelah ditolak

**Recipient:** Seller (pemilik toko)

**Notification:**
- **Title:** `🔄 Bukti Pembayaran Diupload Ulang`
- **Body:** `Pembeli {buyer_name} telah mengunggah ulang bukti pembayaran untuk pesanan #{order_id}`
- **Data:**
  - `type`: `payment_resubmitted`
  - `order_id`: ID pesanan
- **Click Action:** `/seller/payments`

**Code Location:** `app/Http/Controllers/PaymentWebController.php` → `reuploadProof()`

---

## 💬 Chat Notifications

### 7. New Chat Message
**Trigger:** Saat ada pesan baru di chat

**Recipient:** Lawan bicara (buyer atau seller)

**Notification:**
- **Title:** `💬 Pesan dari {sender_name}`
- **Body:** `{message_preview}` (50 karakter pertama)
- **Data:**
  - `type`: `chat_message`
  - `chat_id`: ID chat
  - `order_id`: ID pesanan terkait
  - `sender_id`: ID pengirim
- **Click Action:** `/chat/{order_id}`

**Code Location:** `app/Http/Controllers/ChatController.php` → `store()`

---

## 📊 Notification Summary Table

| Event | Recipient | Icon | Priority | Click Action |
|-------|-----------|------|----------|--------------|
| New Order | Seller | 🛒 | High | `/seller/orders` |
| Order Status Updated | Buyer | 📦 | Medium | `/buyer/orders/{id}` |
| Payment Submitted | Seller | 💳 | High | `/seller/payments` |
| Payment Approved | Buyer | ✅ | High | `/buyer/orders/{id}` |
| Payment Rejected | Buyer | ❌ | High | `/buyer/payments` |
| Payment Resubmitted | Seller | 🔄 | High | `/seller/payments` |
| Chat Message | Both | 💬 | Medium | `/chat/{order_id}` |

---

## 🔧 Implementation Details

### Service Used
All notifications use: `App\Services\PushNotificationService`

### Method Signature
```php
public function sendToUser(
    User $user,
    string $title,
    string $body,
    ?array $data = [],
    ?string $icon = null,
    ?string $clickAction = null
): array
```

### Example Usage
```php
$this->pushNotification->sendToUser(
    $user,
    '🛒 Pesanan Baru!',
    'Pesanan baru dari John Doe senilai Rp 150.000',
    [
        'type' => 'new_order',
        'order_id' => 123,
    ],
    asset('icons/logo-arradea.png'),
    url('/seller/orders')
);
```

---

## 🧪 Testing Notifications

### Test New Order Notification
```bash
php artisan tinker
```

```php
// Create test order
$buyer = App\Models\User::where('role', 'buyer')->first();
$seller = App\Models\User::where('is_seller', true)->first();
$product = $seller->store->products()->first();

// Login as buyer and create order via web interface
// Or use API to create order
```

### Test Payment Notification
```bash
php artisan tinker
```

```php
// Get order with QRIS payment
$order = App\Models\Order::where('payment_method', 'qris')->first();

// Approve payment
$service = app(App\Services\PushNotificationService::class);
$service->sendToUser(
    $order->user,
    '✅ Pembayaran Dikonfirmasi!',
    "Pembayaran untuk pesanan #{$order->id} telah dikonfirmasi",
    ['type' => 'payment_approved', 'order_id' => $order->id],
    asset('icons/logo-arradea.png'),
    url('/buyer/orders/' . $order->id)
);
```

### Test Chat Notification
```bash
php artisan tinker
```

```php
// Get chat
$chat = App\Models\Chat::first();
$sender = $chat->buyer;
$recipient = App\Models\User::find($chat->seller_id);

// Send notification
$service = app(App\Services\PushNotificationService::class);
$service->sendToUser(
    $recipient,
    "💬 Pesan dari {$sender->name}",
    "Halo, apakah produk masih tersedia?",
    [
        'type' => 'chat_message',
        'chat_id' => $chat->id,
        'order_id' => $chat->order_id,
    ],
    asset('icons/logo-arradea.png'),
    url('/chat/' . $chat->order_id)
);
```

---

## 📱 User Experience Flow

### Buyer Journey
1. **Create Order** → Seller receives "New Order" notification
2. **Upload Payment Proof** → Seller receives "Payment Submitted" notification
3. **Wait for Approval** → Buyer receives "Payment Approved" or "Payment Rejected"
4. **If Rejected** → Upload again → Seller receives "Payment Resubmitted"
5. **Order Processing** → Buyer receives "Order Status Updated" notifications
6. **Chat with Seller** → Seller receives "Chat Message" notifications

### Seller Journey
1. **Receive Order** → Get "New Order" notification
2. **Receive Payment Proof** → Get "Payment Submitted" notification
3. **Approve/Reject Payment** → Buyer gets notification
4. **Update Order Status** → Buyer gets "Order Status Updated" notification
5. **Chat with Buyer** → Buyer receives "Chat Message" notifications

---

## 🔍 Debugging Notifications

### Check if notification was sent
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log | grep "FCM"
```

### Expected log output
```
================================================================================
📤 SENDING FCM NOTIFICATION
================================================================================
Title: 🛒 Pesanan Baru!
Body: Pesanan baru dari John Doe senilai Rp 150.000
Number of tokens: 1
FCM Response:
  Successful: 1
  Failed: 0
✅ Notification sent successfully
================================================================================
```

### Check browser console
```javascript
// Should see:
📬 FOREGROUND MESSAGE RECEIVED
Full payload: {...}
🔔 Creating browser notification...
✅ Browser notification created successfully
```

---

## 🎯 Best Practices

### 1. Keep Messages Concise
- Title: Max 50 characters
- Body: Max 100 characters
- Use emojis for visual appeal

### 2. Include Relevant Data
Always include:
- `type`: Notification type
- `order_id` or `chat_id`: Related entity ID
- Any other contextual data

### 3. Set Proper Click Actions
- Direct users to relevant page
- Use absolute URLs
- Test click actions work correctly

### 4. Handle Errors Gracefully
```php
try {
    $this->pushNotification->sendToUser(...);
} catch (\Exception $e) {
    \Log::error('Push notification failed: ' . $e->getMessage());
    // Continue execution - don't break the flow
}
```

### 5. Test All Scenarios
- Foreground (app open)
- Background (app minimized)
- Different browsers
- Different devices

---

## 📈 Future Enhancements

### Potential Additions:
1. **Product Updates** - Notify followers when product is restocked
2. **Promotions** - Notify users about sales/discounts
3. **Review Reminders** - Remind buyers to review completed orders
4. **Low Stock Alerts** - Notify sellers when stock is low
5. **Delivery Updates** - Real-time delivery tracking notifications
6. **Wishlist Alerts** - Notify when wishlist items go on sale

---

## 🔗 Related Documentation

- **FCM Setup:** `README_FCM_FIXES.md`
- **Testing Guide:** `FCM_QUICK_TEST.md`
- **Debug Guide:** `FCM_DEBUG_GUIDE.md`
- **Checklist:** `CHECKLIST_FCM.md`

---

## ✅ Implementation Status

- [x] New Order Notification
- [x] Order Status Update Notification
- [x] Payment Submitted Notification
- [x] Payment Approved Notification
- [x] Payment Rejected Notification
- [x] Payment Resubmitted Notification
- [x] Chat Message Notification
- [x] Comprehensive Logging
- [x] Error Handling
- [x] Documentation

**Status:** ✅ **COMPLETE**

---

**Version:** 1.0  
**Last Updated:** 2026-05-22  
**Author:** Arradea Development Team
