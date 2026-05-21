# 📱 Contoh Penggunaan FCM Push Notification

Dokumen ini berisi contoh-contoh praktis penggunaan Firebase Cloud Messaging di Arradea Marketplace.

---

## 1. Integrasi di Controller yang Sudah Ada

### A. OrderController - Notifikasi Pesanan Baru

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validasi dan buat order
        $order = Order::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'status' => 'pending',
        ]);

        // 🔔 Kirim notifikasi ke seller
        NotificationController::notifyNewOrder($order);

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $order->update(['status' => $request->status]);

        // 🔔 Kirim notifikasi ke buyer
        NotificationController::notifyOrderStatusChange($order, $request->status);

        return back()->with('success', 'Status pesanan berhasil diupdate!');
    }
}
```

### B. PaymentWebController - Notifikasi Pembayaran

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

class PaymentWebController extends Controller
{
    public function approve(Order $order)
    {
        $order->update([
            'payment_status' => 'approved',
            'status' => 'processing'
        ]);

        // 🔔 Kirim notifikasi pembayaran diterima
        NotificationController::notifyPaymentStatus($order, true);

        return back()->with('success', 'Pembayaran berhasil disetujui!');
    }

    public function reject(Order $order, Request $request)
    {
        $order->update([
            'payment_status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        // 🔔 Kirim notifikasi pembayaran ditolak
        NotificationController::notifyPaymentStatus($order, false);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
```

### C. ChatController - Notifikasi Pesan Baru

```php
<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Tentukan recipient (seller atau buyer)
        $chat = $message->chat;
        $recipientId = $chat->seller_id === auth()->id() 
            ? $chat->buyer_id 
            : $chat->seller_id;

        // 🔔 Kirim notifikasi pesan baru
        NotificationController::notifyChatMessage($message, $recipientId);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
```

---

## 2. Notifikasi Custom untuk Berbagai Skenario

### A. Notifikasi Promo/Diskon

```php
// Di AdminController atau PromoController
public function sendPromoNotification(Request $request)
{
    // Kirim ke semua buyer
    $buyerIds = \App\Models\User::where('is_seller', false)
        ->whereNotNull('fcm_token')
        ->pluck('id')
        ->toArray();

    $result = NotificationController::sendPushNotification(
        $buyerIds,
        '🎉 ' . $request->promo_title,
        $request->promo_description,
        [
            'type' => 'promo',
            'promo_id' => $request->promo_id,
            'url' => route('buyer.products', ['promo' => $request->promo_id])
        ],
        $request->promo_image_url
    );

    return back()->with('success', "Notifikasi terkirim ke {$result['sent_count']} user!");
}
```

### B. Notifikasi Produk Baru

```php
// Di ProductController setelah seller create produk
public function store(Request $request)
{
    $product = Product::create([...]);

    // Kirim notifikasi ke buyer yang pernah beli dari toko ini
    $previousBuyerIds = Order::where('store_id', $product->store_id)
        ->distinct('user_id')
        ->pluck('user_id')
        ->toArray();

    if (!empty($previousBuyerIds)) {
        NotificationController::sendPushNotification(
            $previousBuyerIds,
            '🆕 Produk Baru dari ' . $product->store->name,
            $product->name . ' - ' . number_format($product->price, 0, ',', '.'),
            [
                'type' => 'new_product',
                'product_id' => $product->id,
                'url' => route('buyer.products', $product->id)
            ],
            $product->image ? asset('storage/' . $product->image) : null
        );
    }

    return redirect()->route('seller.products')
        ->with('success', 'Produk berhasil ditambahkan!');
}
```

### C. Notifikasi Stok Hampir Habis (ke Seller)

```php
// Di ProductObserver atau setelah order
public function updated(Product $product)
{
    // Jika stok <= 5, kirim notifikasi ke seller
    if ($product->stock <= 5 && $product->stock > 0) {
        $seller = $product->store->user;

        NotificationController::sendPushNotification(
            $seller->id,
            '⚠️ Stok Hampir Habis!',
            "Produk {$product->name} tinggal {$product->stock} item",
            [
                'type' => 'low_stock',
                'product_id' => $product->id,
                'url' => route('seller.products.edit', $product->id)
            ]
        );
    }
}
```

### D. Notifikasi Reminder Pembayaran

```php
// Di scheduled command atau job
public function handle()
{
    // Cari order yang pending > 24 jam
    $pendingOrders = Order::where('status', 'pending')
        ->where('created_at', '<', now()->subHours(24))
        ->where('created_at', '>', now()->subHours(48))
        ->get();

    foreach ($pendingOrders as $order) {
        NotificationController::sendPushNotification(
            $order->user_id,
            '⏰ Reminder Pembayaran',
            "Pesanan #{$order->id} menunggu pembayaran. Segera upload bukti pembayaran!",
            [
                'type' => 'payment_reminder',
                'order_id' => $order->id,
                'url' => route('buyer.orders.show', $order->id)
            ]
        );
    }
}
```

### E. Notifikasi Seller Application Approved

```php
// Di AdminUserController setelah approve seller
public function approveSeller(User $user)
{
    $user->update([
        'is_seller' => true,
        'seller_status' => 'approved',
        'seller_approved_at' => now(),
    ]);

    // 🔔 Kirim notifikasi approval
    NotificationController::sendPushNotification(
        $user->id,
        '✅ Selamat! Akun Seller Disetujui',
        'Kamu sekarang bisa mulai berjualan di Arradea Marketplace!',
        [
            'type' => 'seller_approved',
            'url' => route('seller.dashboard')
        ]
    );

    return back()->with('success', 'Seller berhasil disetujui!');
}
```

---

## 3. Notifikasi Terjadwal (Scheduled Notifications)

### Setup Laravel Scheduler

Edit `app/Console/Kernel.php`:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\NotificationController;
use App\Models\Order;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Reminder pembayaran setiap jam
        $schedule->call(function () {
            $pendingOrders = Order::where('status', 'pending')
                ->where('created_at', '<', now()->subHours(24))
                ->where('created_at', '>', now()->subHours(48))
                ->get();

            foreach ($pendingOrders as $order) {
                NotificationController::sendPushNotification(
                    $order->user_id,
                    '⏰ Reminder Pembayaran',
                    "Pesanan #{$order->id} menunggu pembayaran",
                    ['type' => 'payment_reminder', 'order_id' => $order->id]
                );
            }
        })->hourly();

        // Notifikasi promo harian (setiap pagi jam 9)
        $schedule->call(function () {
            $buyerIds = \App\Models\User::where('is_seller', false)
                ->whereNotNull('fcm_token')
                ->pluck('id')
                ->toArray();

            NotificationController::sendPushNotification(
                $buyerIds,
                '🌅 Selamat Pagi!',
                'Cek produk-produk terbaru hari ini di Arradea!',
                ['type' => 'daily_promo', 'url' => route('buyer.products')]
            );
        })->dailyAt('09:00');
    }
}
```

Jalankan scheduler:

```bash
# Development (manual)
php artisan schedule:work

# Production (cron job)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 4. Notifikasi dengan Action Buttons

### Update Service Worker

Edit `public/firebase-messaging-sw.js`:

```javascript
messaging.onBackgroundMessage((payload) => {
    const { notification, data } = payload;

    const notificationOptions = {
        body: notification.body,
        icon: notification.icon || '/images/logo.png',
        badge: '/images/badge.png',
        data: data,
        actions: [
            {
                action: 'view',
                title: 'Lihat',
                icon: '/images/icon-view.png'
            },
            {
                action: 'dismiss',
                title: 'Tutup',
                icon: '/images/icon-close.png'
            }
        ]
    };

    return self.registration.showNotification(notification.title, notificationOptions);
});

// Handle action button clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'view') {
        const urlToOpen = event.notification.data?.url || '/';
        event.waitUntil(clients.openWindow(urlToOpen));
    }
    // 'dismiss' action tidak perlu handling, notifikasi sudah ditutup
});
```

### Kirim Notifikasi dengan Actions

```php
NotificationController::sendPushNotification(
    $userId,
    'Pesanan Baru!',
    'Kamu mendapat pesanan baru',
    [
        'type' => 'new_order',
        'order_id' => $order->id,
        'url' => route('seller.orders'),
        'actions' => json_encode([
            ['action' => 'view', 'title' => 'Lihat Pesanan'],
            ['action' => 'dismiss', 'title' => 'Nanti Saja']
        ])
    ]
);
```

---

## 5. Notifikasi Berdasarkan User Preferences

### Tambah Kolom Preferences di Users Table

```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->json('notification_preferences')->nullable();
});
```

### Update User Model

```php
// app/Models/User.php
protected $casts = [
    'notification_preferences' => 'array',
];

public function wantsNotification(string $type): bool
{
    $preferences = $this->notification_preferences ?? [];
    return $preferences[$type] ?? true; // default: enabled
}
```

### Cek Preferences Sebelum Kirim

```php
// Sebelum kirim notifikasi
$user = User::find($userId);

if ($user->wantsNotification('new_order')) {
    NotificationController::sendPushNotification(
        $userId,
        'Pesanan Baru!',
        'Kamu mendapat pesanan baru',
        ['type' => 'new_order']
    );
}
```

### UI untuk Manage Preferences

```blade
<!-- resources/views/profile/notifications.blade.php -->
<form method="POST" action="{{ route('profile.notifications.update') }}">
    @csrf
    
    <label>
        <input type="checkbox" name="preferences[new_order]" 
               {{ auth()->user()->notification_preferences['new_order'] ?? true ? 'checked' : '' }}>
        Pesanan Baru
    </label>
    
    <label>
        <input type="checkbox" name="preferences[order_status]" 
               {{ auth()->user()->notification_preferences['order_status'] ?? true ? 'checked' : '' }}>
        Status Pesanan
    </label>
    
    <label>
        <input type="checkbox" name="preferences[promo]" 
               {{ auth()->user()->notification_preferences['promo'] ?? true ? 'checked' : '' }}>
        Promo & Diskon
    </label>
    
    <button type="submit">Simpan Preferensi</button>
</form>
```

---

## 6. Logging & Monitoring

### Log Setiap Notifikasi Terkirim

```php
// Di NotificationController::sendPushNotification()
Log::channel('fcm')->info('Push notification sent', [
    'recipients' => count($fcmTokens),
    'title' => $title,
    'success_count' => $successCount,
    'failure_count' => $failureCount,
    'timestamp' => now()
]);
```

### Setup Log Channel untuk FCM

Edit `config/logging.php`:

```php
'channels' => [
    'fcm' => [
        'driver' => 'daily',
        'path' => storage_path('logs/fcm.log'),
        'level' => 'info',
        'days' => 14,
    ],
],
```

### Buat Dashboard Monitoring (Optional)

```php
// routes/web.php
Route::get('/admin/fcm-stats', function () {
    $totalUsers = \App\Models\User::whereNotNull('fcm_token')->count();
    $totalSent = // ambil dari log atau database
    $successRate = // hitung success rate
    
    return view('admin.fcm-stats', compact('totalUsers', 'totalSent', 'successRate'));
})->middleware(['auth', 'role:admin']);
```

---

## 7. Best Practices

### ✅ DO

- Kirim notifikasi yang relevan dan penting
- Gunakan title dan body yang jelas dan singkat
- Sertakan deep link (URL) ke halaman terkait
- Test notifikasi sebelum deploy ke production
- Log setiap notifikasi untuk monitoring
- Respect user preferences (jangan spam)

### ❌ DON'T

- Jangan kirim notifikasi terlalu sering (spam)
- Jangan kirim notifikasi marketing tanpa consent
- Jangan kirim notifikasi di tengah malam (kecuali urgent)
- Jangan hardcode FCM keys di code (gunakan .env)
- Jangan lupa handle error dan edge cases

---

## 8. Error Handling

```php
public static function sendPushNotification($userIds, string $title, string $body, array $data = [], ?string $image = null): array
{
    try {
        // ... existing code ...
        
        // Handle invalid tokens
        if (isset($responseData['results'])) {
            foreach ($responseData['results'] as $index => $result) {
                if (isset($result['error'])) {
                    $errorType = $result['error'];
                    $token = $fcmTokens[$index];
                    
                    // Remove invalid tokens
                    if (in_array($errorType, ['InvalidRegistration', 'NotRegistered'])) {
                        \App\Models\User::where('fcm_token', $token)
                            ->update(['fcm_token' => null]);
                        
                        Log::warning('Removed invalid FCM token', [
                            'token' => substr($token, 0, 20) . '...',
                            'error' => $errorType
                        ]);
                    }
                }
            }
        }
        
        return [
            'success' => true,
            'sent_count' => $successCount,
            'failed_count' => $failureCount
        ];
    } catch (\Exception $e) {
        Log::error('FCM Error', ['message' => $e->getMessage()]);
        return ['success' => false, 'sent_count' => 0];
    }
}
```

---

**Happy Coding! 🚀**
