# Mode Switcher - DANA Style (Mobile Only)

## 📱 Fitur

Bottom sheet mode switcher yang mirip dengan aplikasi DANA, dioptimalkan untuk tampilan mobile.

### ✨ Highlights:
- **Swipe to Close**: Geser ke bawah untuk menutup bottom sheet
- **Smooth Animation**: Animasi halus dan responsif
- **Mobile Optimized**: Didesain khusus untuk mobile (responsive)
- **Active State**: Menampilkan mode yang sedang aktif dengan visual yang jelas
- **Disabled State**: Mode seller disabled jika belum disetujui admin
- **Touch Gestures**: Support drag gesture untuk menutup

## 🎨 Desain

Desain mengikuti prinsip DANA:
- Bottom sheet muncul dari bawah
- Drag handle untuk visual feedback
- Gradient background untuk mode aktif
- Icon dan badge yang jelas
- Smooth transitions

## 📂 File Structure

```
resources/views/components/
└── bottom-sheet-switcher.blade.php  # Komponen utama

resources/views/
├── profile.blade.php                # Implementasi di profile
└── mode-switcher-demo.blade.php     # Halaman demo

routes/
└── web.php                          # Route definitions
```

## 🚀 Cara Menggunakan

### 1. Di Blade Template

```blade
<x-bottom-sheet-switcher :user="auth()->user()" />
```

### 2. Route yang Diperlukan

Route sudah tersedia di `routes/web.php`:
```php
Route::post('/mode/switch', [ModeController::class, 'switch'])->name('mode.switch');
```

### 3. Testing

Akses halaman demo:
```
/mode-switcher-demo
```

Atau lihat implementasi di:
```
/profile
```

## 🎯 Logic Flow

1. User klik tombol trigger
2. Bottom sheet muncul dari bawah dengan animasi
3. User pilih mode (Buyer/Seller)
4. Form submit ke `/mode/switch`
5. ModeController memproses switch
6. Redirect kembali dengan flash message
7. Bottom sheet tertutup otomatis

## 🔧 Customization

### Warna Mode

Edit di `bottom-sheet-switcher.blade.php`:

**Buyer Mode (Biru):**
```css
.dana-option-buyer-active {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #3b82f6;
}
```

**Seller Mode (Kuning/Amber):**
```css
.dana-option-seller-active {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #f59e0b;
}
```

### Animasi

Ubah durasi animasi:
```blade
x-transition:enter="transition ease-out duration-400 transform"
x-transition:leave="transition ease-in duration-300 transform"
```

## 📱 Mobile Optimization

Komponen ini dioptimalkan untuk mobile dengan:
- Touch gestures (swipe down to close)
- Minimum touch target 44px
- Responsive padding dan spacing
- Smooth scrolling
- Prevent body scroll saat sheet terbuka

## 🔐 Permission Logic

Mode Seller hanya bisa diakses jika:
```php
$user->canSwitchToSellerMode()
```

Yang mengecek:
- User sudah approved sebagai seller (`is_seller = true`)
- Store status = 'active'

## 🎨 Visual States

### Active State
- Border berwarna (biru/kuning)
- Background gradient
- Badge "Aktif"
- Checkmark icon

### Inactive State
- Border abu-abu
- Background putih
- Arrow icon

### Disabled State
- Opacity 60%
- Cursor not-allowed
- Lock icon
- Text abu-abu

## 📝 Notes

- Komponen menggunakan Alpine.js untuk interaktivity
- Styling inline untuk kemudahan maintenance
- Mobile-first approach
- Tidak ada dependency eksternal selain Alpine.js

## 🐛 Troubleshooting

**Bottom sheet tidak muncul:**
- Pastikan Alpine.js sudah loaded
- Check console untuk error JavaScript

**Swipe gesture tidak bekerja:**
- Pastikan touch events tidak di-block oleh parent element
- Check z-index conflicts

**Mode tidak berubah:**
- Check route `/mode/switch` sudah terdaftar
- Verify ModeController logic
- Check user permissions

## 🎉 Demo

Akses: `/mode-switcher-demo`

Demo page menampilkan:
- Current active mode
- Mode switcher component
- Feature list
- User info
- Back to profile button
