# Fitur Otomatis Arradea

## 1. Jam Buka/Tutup Toko Otomatis

### Cara Kerja
- Seller dapat mengatur jam buka dan tutup toko
- Status toko akan otomatis berubah sesuai jadwal yang ditentukan
- Menggunakan timezone WIB (Asia/Jakarta)
- Mendukung jam operasional overnight (misal: 22:00 - 02:00)

### Pengaturan
1. Login sebagai seller
2. Buka dashboard seller
3. Atur jam buka dan tutup
4. Centang "Auto" untuk mengaktifkan jadwal otomatis
5. Klik "Simpan Jadwal"

### Cara Kerja Teknis
- **Middleware**: `SyncSellerStoreSchedule` - Sinkronisasi saat seller melakukan request
- **Scheduled Task**: Command `stores:sync-schedules` berjalan setiap menit
- **Command Manual**: `php artisan stores:sync-schedules`

### Setup Scheduled Task
Tambahkan ke crontab server:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan scheduler di development:
```bash
php artisan schedule:work
```

---

## 2. Diskon Produk Otomatis

### Cara Kerja
- Seller dapat mengatur diskon dengan periode waktu tertentu
- Diskon otomatis aktif/nonaktif sesuai tanggal & waktu yang ditentukan
- Harga final dihitung secara real-time
- Menggunakan timezone WIB (Asia/Jakarta)

### Pengaturan Diskon
1. Login sebagai seller
2. Buka halaman edit produk
3. Atur:
   - **Discount Percent**: Persentase diskon (0-100%)
   - **Discount Start**: Tanggal & waktu mulai diskon
   - **Discount End**: Tanggal & waktu berakhir diskon
4. Simpan produk

### Fitur Diskon
- **Diskon Produk**: Berlaku untuk semua varian
- **Diskon Varian**: Berlaku hanya untuk varian tertentu (override diskon produk)
- **Auto Calculate**: Harga final dihitung otomatis
- **Real-time**: Status diskon dicek setiap kali produk ditampilkan

### Accessor yang Tersedia
Saat mengambil data produk, otomatis tersedia:
- `final_price`: Harga setelah diskon
- `has_active_discount`: Boolean apakah ada diskon aktif
- `active_discount_percent`: Persentase diskon yang sedang aktif

### Contoh Penggunaan di Blade
```blade
<div class="product-card">
    <h3>{{ $product->name }}</h3>
    
    @if($product->has_active_discount)
        <span class="badge">Diskon {{ $product->active_discount_percent }}%</span>
        <div class="price">
            <span class="original">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            <span class="final">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
        </div>
    @else
        <div class="price">
            <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
        </div>
    @endif
</div>
```

### Contoh Response API
```json
{
    "id": 1,
    "name": "Produk A",
    "price": 100000,
    "discount_percent": 20,
    "discount_start_at": "2026-05-01 00:00:00",
    "discount_end_at": "2026-05-31 23:59:59",
    "final_price": 80000,
    "has_active_discount": true,
    "active_discount_percent": 20
}
```

---

## Testing

### Test Jam Buka/Tutup
```bash
# Jalankan command manual
php artisan stores:sync-schedules

# Lihat log
tail -f storage/logs/laravel.log
```

### Test Diskon
```php
// Di tinker
php artisan tinker

$product = Product::find(1);
$product->discount_percent = 20;
$product->discount_start_at = now();
$product->discount_end_at = now()->addDays(7);
$product->save();

// Cek hasil
$product->fresh();
echo $product->has_active_discount; // true
echo $product->active_discount_percent; // 20
echo $product->final_price; // harga setelah diskon
```

---

## Troubleshooting

### Jam Toko Tidak Berubah Otomatis
1. Pastikan cron job sudah berjalan: `crontab -l`
2. Cek apakah `auto_schedule` = true di database
3. Cek kolom `open_time` dan `close_time` tidak null
4. Jalankan manual: `php artisan stores:sync-schedules`

### Diskon Tidak Muncul
1. Pastikan tanggal diskon sudah benar (timezone WIB)
2. Cek `discount_start_at` <= sekarang
3. Cek `discount_end_at` >= sekarang
4. Cek `discount_percent` > 0
5. Clear cache: `php artisan cache:clear`

---

## Catatan Penting

1. **Timezone**: Semua waktu menggunakan WIB (Asia/Jakarta)
2. **Scheduled Task**: Harus setup cron job di server production
3. **Performance**: Accessor dihitung real-time, pertimbangkan caching untuk traffic tinggi
4. **Validation**: Pastikan validasi input di controller untuk mencegah data invalid
