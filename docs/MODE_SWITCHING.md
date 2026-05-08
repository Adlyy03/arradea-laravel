# 🔄 Mode Switching Feature - Arradea Marketplace

## 📋 Overview

Fitur **Mode Switching** memungkinkan user yang sudah menjadi seller untuk beralih antara **Mode Buyer** dan **Mode Seller** tanpa perlu logout, mirip seperti aplikasi DANA yang bisa switch antara mode personal dan bisnis.

## 🎯 Fitur Utama

- ✅ **Session-based switching**: Mode aktif disimpan di session, bukan mengubah database setiap kali switch
- ✅ **Preferred mode**: Pilihan terakhir user disimpan di database untuk diingat saat login berikutnya
- ✅ **Middleware integration**: RoleMiddleware otomatis check mode aktif dari session
- ✅ **Auto-initialize on login**: Mode otomatis di-set sesuai preferred_mode saat user login
- ✅ **Mobile-first UI**: Bottom sheet dengan touch-friendly design (44x44px minimum)
- ✅ **Smooth animations**: Transisi 300ms dengan haptic feedback simulation
- ✅ **API support**: Endpoint tersedia untuk mobile app

## 🏗️ Arsitektur

### Database Schema

```sql
ALTER TABLE users ADD COLUMN preferred_mode ENUM('buyer', 'seller') DEFAULT 'buyer';
```

### Session Structure

```php
session([
    'active_mode' => 'seller' // or 'buyer'
]);
```

### Flow Diagram

```
┌─────────────┐
│   User      │
│   Login     │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────┐
│ SellerModeService           │
│ initializeModeOnLogin()     │
│                             │
│ • Read preferred_mode       │
│ • Validate access           │
│ • Set session('active_mode')│
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│ RoleMiddleware              │
│                             │
│ • Check session('active_mode')│
│ • Validate seller access    │
│ • Allow/deny route access   │
└─────────────────────────────┘
```

## 📁 File Structure

```
app/
├── Models/
│   └── User.php                    # Mode methods added
├── Services/
│   └── SellerModeService.php       # Business logic
├── Http/
│   ├── Controllers/
│   │   ├── ModeController.php      # Switch & info endpoints
│   │   └── AuthWebController.php   # Initialize mode on login
│   └── Middleware/
│       └── RoleMiddleware.php      # Check active_mode

resources/views/
├── components/
│   ├── mode-badge.blade.php        # Mode indicator badge
│   └── bottom-sheet-switcher.blade.php  # Mobile switcher UI
└── layouts/
    └── app.blade.php               # Navbar with mode switcher

database/migrations/
└── 2026_05_08_000001_add_seller_mode_fields_to_users_table.php

config/
└── mode.php                        # Mode configuration

routes/
├── web.php                         # Web routes
└── api.php                         # API routes

docs/
└── MODE_SWITCHING.md               # This file
```

## 🔧 API Endpoints

### Web Routes

```php
POST /mode/switch
GET  /mode/info
```

### API Routes (Mobile App)

```php
POST /api/mode/switch
GET  /api/mode/info
```

## 📖 Usage Examples

### 1. Check Current Mode

```php
$user = Auth::user();
$activeMode = $user->getActiveMode(); // 'buyer' or 'seller'
$isSeller = $user->isInSellerMode(); // boolean
```

### 2. Switch Mode Programmatically

```php
use App\Services\SellerModeService;

$modeService = app(SellerModeService::class);
$result = $modeService->switchMode($user, 'seller');

// Result:
// [
//     'success' => true,
//     'message' => '🎉 Selamat berjualan di Arradea!',
//     'mode' => 'seller'
// ]
```

### 3. Check if User Can Switch to Seller

```php
if ($user->canSwitchToSellerMode()) {
    // User is seller AND approved
}
```

### 4. Use Mode Badge Component

```blade
<x-mode-badge :mode="Auth::user()->getActiveMode()" />
```

### 5. Use Bottom Sheet Switcher

```blade
<x-bottom-sheet-switcher :user="Auth::user()" />
```

## 🎨 UI Components

### Mode Badge

Compact indicator showing current mode:

```blade
<x-mode-badge mode="seller" />
<!-- Output: 🏪 Seller (purple badge) -->

<x-mode-badge mode="buyer" />
<!-- Output: 🛒 Buyer (blue badge) -->
```

### Bottom Sheet Switcher

Mobile-first drawer for switching modes:

```blade
<x-bottom-sheet-switcher :user="$user" />
```

Features:
- Touch-friendly (44x44px minimum)
- Smooth slide-up animation
- Haptic feedback simulation
- Disabled state for non-sellers
- Active mode indicator

## 🔐 Security & Validation

### Middleware Protection

```php
// Seller routes require BOTH is_seller AND active_mode === 'seller'
Route::middleware('role:seller')->group(function () {
    Route::get('/seller/dashboard', ...);
});
```

### Business Logic Validation

```php
// SellerModeService validates:
1. User has is_seller = true
2. User has seller_status = 'approved'
3. Target mode is valid ('buyer' or 'seller')
4. User is not already in target mode
```

## 🧪 Testing

### Manual Testing

1. Login sebagai seller yang sudah approved
2. Buka navbar dropdown (klik avatar)
3. Klik tombol mode switcher
4. Bottom sheet akan muncul
5. Pilih mode yang diinginkan
6. Halaman akan reload dengan mode baru

### Feature Test

```bash
php artisan test --filter ModeSwitchTest
```

## 🚀 Deployment Checklist

- [x] Run migration: `php artisan migrate`
- [x] Clear caches: `php artisan config:clear && php artisan cache:clear && php artisan view:clear`
- [x] Test as seller user
- [x] Verify mode switching works
- [x] Check middleware protection
- [x] Test API endpoints (if using mobile app)

## 🐛 Troubleshooting

### Issue: Mode tidak berubah setelah switch

**Solution:**
```bash
php artisan cache:clear
php artisan session:clear
```

### Issue: Seller tidak bisa akses dashboard setelah switch

**Possible causes:**
1. `seller_status` bukan 'approved'
2. Session tidak ter-set dengan benar
3. Middleware cache issue

**Solution:**
```php
// Check user status
$user = Auth::user();
dd([
    'is_seller' => $user->is_seller,
    'seller_status' => $user->seller_status,
    'active_mode' => session('active_mode'),
    'preferred_mode' => $user->preferred_mode,
]);
```

### Issue: Bottom sheet tidak muncul

**Possible causes:**
1. Alpine.js tidak loaded
2. Component tidak di-include
3. User bukan seller

**Solution:**
```blade
{{-- Pastikan Alpine.js loaded --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Check user status --}}
@if(Auth::user()->canSwitchToSellerMode())
    <x-bottom-sheet-switcher :user="Auth::user()" />
@endif
```

## 📊 Performance Considerations

- **Session storage**: Mode aktif disimpan di session (fast read/write)
- **Database writes**: Hanya saat switch mode (update preferred_mode)
- **Middleware overhead**: Minimal (1 session read per request)
- **No N+1 queries**: Mode check tidak trigger additional queries

## 🔮 Future Enhancements

Possible improvements:
- [ ] Mode history tracking (log setiap switch)
- [ ] Analytics per mode (revenue, orders, etc.)
- [ ] Scheduled mode switching (auto-switch based on time)
- [ ] Multi-store support (switch between different stores)
- [ ] Mode-specific notifications
- [ ] Quick switch shortcut (keyboard: Ctrl+M)

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development Arradea.

---

**Last Updated:** May 8, 2026  
**Version:** 1.0.0  
**Author:** Arradea Development Team
