# 🚀 Quick Start - Fitur Otomatis Arradea

## 📦 Apa yang Sudah Diperbaiki?

### ✅ 1. Jam Buka/Tutup Toko Otomatis (WIB)
- Status toko berubah otomatis sesuai jadwal
- Scheduled task berjalan setiap menit
- Mendukung jam overnight (22:00 - 02:00)

### ✅ 2. Diskon Produk Otomatis (WIB)
- Harga diskon dihitung real-time
- Accessor otomatis di JSON response
- Periode diskon dengan tanggal mulai & berakhir

---

## ⚡ Setup Cepat

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Setup Scheduled Task (Production)
```bash
# Edit crontab
crontab -e

# Tambahkan baris ini:
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Development (Tanpa Cron)
```bash
# Jalankan scheduler worker
php artisan schedule:work

# Atau test manual
php artisan stores:sync-schedules
```

---

## 🎯 Cara Pakai

### Tampilkan Harga dengan Diskon
```blade
{{-- Otomatis tampil harga diskon --}}
<x-product-price :product="$product" />

{{-- Atau manual --}}
@if($product->has_active_discount)
    <span class="badge">{{ $product->active_discount_percent }}% OFF</span>
    <span class="original">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
    <span class="final">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
@else
    <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
@endif
```

### Tampilkan Status Toko
```blade
{{-- Component status toko --}}
<x-store-status :store="$store" />
<x-store-status :seller="Auth::user()" />
```

### API Response (Otomatis)
```json
{
    "id": 1,
    "name": "Produk A",
    "price": 100000,
    "final_price": 80000,
    "has_active_discount": true,
    "active_discount_percent": 20
}
```

---

## 🧪 Testing Cepat

### Test Diskon
```bash
php artisan tinker
```
```php
$p = Product::first();
$p->discount_percent = 25;
$p->discount_start_at = now();
$p->discount_end_at = now()->addDays(7);
$p->save();

// Cek
$p->fresh();
echo $p->has_active_discount ? 'Aktif' : 'Tidak';
echo "\nFinal: " . $p->final_price;
```

### Test Jam Toko
```bash
php artisan stores:sync-schedules
```

---

## 📚 Dokumentasi Lengkap

- **`FITUR_OTOMATIS.md`** - Penjelasan detail cara kerja
- **`CONTOH_PENGGUNAAN.md`** - 10+ contoh code snippet
- **`PERBAIKAN_SUMMARY.md`** - Summary lengkap perbaikan

---

## ⚠️ Penting!

1. **Timezone**: Semua waktu menggunakan **WIB (Asia/Jakarta)**
2. **Cron Job**: Wajib setup di production
3. **Index**: Migration index sudah dijalankan untuk performa optimal

---

## 🆘 Troubleshooting

### Jam toko tidak berubah?
```bash
# Cek scheduled task
php artisan schedule:list

# Test manual
php artisan stores:sync-schedules

# Cek log
tail -f storage/logs/laravel.log
```

### Diskon tidak muncul?
- Cek tanggal `discount_start_at` <= sekarang
- Cek tanggal `discount_end_at` >= sekarang
- Cek `discount_percent` > 0
- Clear cache: `php artisan cache:clear`

---

**Timezone**: WIB (Asia/Jakarta)  
**Updated**: 11 Mei 2026
