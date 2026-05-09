# Skenario Uji: Buyer Beralih Menjadi Seller

## Deskripsi Keseluruhan
Uji skenario lengkap di mana pengguna dengan peran **buyer** dapat:
1. ✅ Beralih menjadi **seller**
2. ✅ Mengunggah produk ke toko mereka
3. ✅ Memastikan produk terlihat oleh pengguna lain (buyer)
4. ✅ Mengelola produk (update, delete)
5. ✅ Melihat dampak perubahan secara real-time

---

## Test Suite: `BuyerToSellerWorkflowTest`

Lokasi file: `tests/Feature/BuyerToSellerWorkflowTest.php`

### Setup Awal
- **AccessCode**: `BUYER-SELLER-TEST` (aktif)
- **Lokasi Config**: 
  - Center: -6.200000, 106.816666 (Jakarta)
  - Max Radius: 5 km
- **Pengguna Uji**:
  - `$buyer`: Pembeli yang akan menjadi penjual
  - `$otherBuyer`: Pembeli lain untuk memverifikasi visibilitas

---

## Test Cases

### 1. **test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers**
**Tujuan**: Menguji alur lengkap buyer→seller→product→visibility

**Langkah-Langkah**:
```
1. VERIFY: Buyer awal bukan seller (is_seller = false, store = null)
   └─ Assert: $buyer->is_seller === false

2. TOGGLE SELLER MODE
   └─ PATCH /api/profile/seller-mode
   └─ Body: { enable: true, store_name: "Toko Elektronik Budi", ... }
   └─ Assert: Response 200 OK, success = true

3. VERIFY SELLER STATUS
   └─ Reload user dari database
   └─ Assert: is_seller = true
   └─ Assert: store != null
   └─ Assert: store.name = "Toko Elektronik Budi"

4. CREATE PRODUCT
   └─ POST /api/products
   └─ Body: { name: "Smartphone XYZ", price: 4500000, stock: 15, ... }
   └─ Assert: Response 201 Created, success = true
   └─ Extract: productId = response.data.id

5. VERIFY IN DATABASE
   └─ AssertDatabaseHas: products table memiliki record baru
   └─ Verifikasi: store_id, name, price, stock

6. VERIFY IN PUBLIC LISTING
   └─ GET /api/products (tanpa auth)
   └─ Assert: Produk ada di dalam data.data
   └─ Verifikasi: name, price, stock, store.name

7. VERIFY BY OTHER BUYER
   └─ Ganti pengguna ke $otherBuyer
   └─ GET /api/products/{productId}
   └─ Assert: Response 200 OK
   └─ Verifikasi: data.name, data.price, data.stock

8. VERIFY SEARCH FUNCTIONALITY
   └─ GET /api/products/search?q=Smartphone
   └─ Assert: Produk ditemukan dalam results
   └─ Verifikasi: name dan store info
```

**Hasil Diharapkan**: ✅ Produk terlihat oleh pembeli lain di listing publik

---

### 2. **test_seller_uploads_multiple_products_all_visible_to_buyers**
**Tujuan**: Memverifikasi multiple products dari seller yang sama semuanya terlihat

**Langkah-Langkah**:
```
1. Seller toggle ke mode seller
2. Create 3 produk berbeda:
   - Laptop Pro 15 (Rp 15.000.000)
   - Monitor 4K (Rp 3.500.000)
   - Keyboard Mechanical (Rp 1.200.000)

3. Other buyer lihat listing
4. Verifikasi semua 3 produk ada di dalam list
5. Verifikasi store.name konsisten untuk ketiga produk
```

**Hasil Diharapkan**: ✅ Semua produk terlihat dengan info toko yang konsisten

---

### 3. **test_seller_modifies_product_changes_visible_to_buyers**
**Tujuan**: Memverifikasi bahwa perubahan produk langsung terlihat oleh buyers

**Langkah-Langkah**:
```
1. Seller create produk:
   - Name: "T-Shirt Original"
   - Price: 150000
   - Stock: 50

2. Seller update produk:
   - Name: "T-Shirt Premium Edition"
   - Price: 250000
   - Stock: 30
   
3. Other buyer lihat detail produk
4. Verifikasi: Nama, harga, dan stok sudah terupdate
```

**Hasil Diharapkan**: ✅ Perubahan langsung terlihat oleh buyers

---

### 4. **test_seller_deletes_product_no_longer_visible_to_buyers**
**Tujuan**: Memverifikasi bahwa produk yang dihapus tidak lagi terlihat

**Langkah-Langkah**:
```
1. Seller create produk: "Book: Laravel Guide"
2. Other buyer verifikasi produk terlihat (GET /api/products/{id})
3. Seller delete produk (DELETE /api/products/{id})
4. Verifikasi: Produk tidak ada di database
5. Other buyer coba lihat produk
   └─ Assert: Response 404 Not Found
```

**Hasil Diharapkan**: ✅ Produk tidak lagi dapat diakses

---

### 5. **test_inactive_seller_products_not_visible_to_buyers**
**Tujuan**: Memverifikasi filter visibilitas - hanya produk dari toko aktif yang terlihat

**Langkah-Langkah**:
```
1. Create seller dengan store.status = 'inactive'
2. Create produk di toko inactive
3. Other buyer lihat listing
4. Verifikasi: Produk tidak ada di listing
5. Other buyer coba akses langsung
   └─ Assert: Response 404 Not Found
```

**Kondisi Filter di ProductController**:
```php
->whereHas('store.user', function ($q) {
    $q->where('is_seller', true);
})
->whereHas('store', function ($q) {
    $q->where('status', 'active');
})
```

**Hasil Diharapkan**: ✅ Produk dari toko inactive tidak terlihat

---

### 6. **test_seller_switches_back_to_buyer_products_remain**
**Tujuan**: Memverifikasi bahwa toggle seller mode off tidak menghapus produk

**Langkah-Langkah**:
```
1. Seller create produk
2. Other buyer verifikasi produk terlihat
3. Seller toggle seller mode OFF
   └─ PATCH /api/profile/seller-mode { enable: false }
4. Verifikasi: is_seller = false
5. Produk masih ada di database
6. Other buyer masih bisa lihat produk
7. Original seller (now buyer) coba create produk
   └─ Assert: Response 403 Forbidden (role middleware)
```

**Hasil Diharapkan**: ✅ Produk tetap terlihat, seller tidak bisa membuat produk baru

---

## Endpoints yang Diuji

| Endpoint | Method | Role | Deskripsi |
|----------|--------|------|-----------|
| `/api/profile/seller-mode` | PATCH | Buyer/Seller | Toggle seller mode on/off |
| `/api/products` | POST | Seller | Create produk |
| `/api/products` | GET | Public | List semua produk |
| `/api/products/{id}` | GET | Public | Detail produk |
| `/api/products/search` | GET | Public | Search produk |
| `/api/products/{id}` | PUT | Seller | Update produk |
| `/api/products/{id}` | DELETE | Seller | Delete produk |

---

## Verifikasi Database

Test menggunakan `RefreshDatabase` - setiap test berjalan dengan database bersih.

### Tables yang Terlibat
- `users` - Buyer dan seller
- `access_codes` - Kode akses
- `stores` - Toko seller
- `products` - Produk yang dijual
- `categories` - Kategori produk

---

## Cara Menjalankan Test

### Opsi 1: Run semua tests di file ini
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php
```

### Opsi 2: Run dengan verbose output
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --verbose
```

### Opsi 3: Run test tertentu
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --filter=test_buyer_switches_to_seller
```

### Opsi 4: Run dengan coverage report
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --coverage
```

---

## Expected Output

Ketika semua test berhasil:
```
Tests\Feature\BuyerToSellerWorkflowTest
  ✓ test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers
  ✓ test_seller_uploads_multiple_products_all_visible_to_buyers
  ✓ test_seller_modifies_product_changes_visible_to_buyers
  ✓ test_seller_deletes_product_no_longer_visible_to_buyers
  ✓ test_inactive_seller_products_not_visible_to_buyers
  ✓ test_seller_switches_back_to_buyer_products_remain

6 tests passed
```

---

## Catatan Implementasi

### Authentication Flow
- Test menggunakan `Laravel\Sanctum\Sanctum::actingAs($user)` untuk authenticate
- Setiap perubahan user di-refresh dengan `.fresh()` untuk memastikan state terbaru

### Role-Based Middleware
- Seller endpoints dilindungi dengan middleware `role:seller`
- Middleware harus memverifikasi `is_seller === true`

### Cache Handling
- Ketika produk dibuat/update/delete, `CacheService::clearProductCache()` dipanggil
- Public API endpoints menggunakan cache 5 menit untuk listing

### Location Validation
- Login memverifikasi lokasi user berdasarkan config `location.center_lat/lng` dan `location.max_radius`
- Test meng-override config di setUp() method

---

## Troubleshooting

### Test Gagal: "You do not have a store yet"
**Penyebab**: User tidak di-refresh setelah toggle seller mode
**Solusi**: Pastikan ada `Sanctum::actingAs($user->fresh())` setelah toggle

### Test Gagal: Produk tidak muncul di listing
**Penyebab**: Store status bukan 'active' atau is_seller bukan true
**Solusi**: Verifikasi toggle seller mode berhasil dan store dibuat

### Test Gagal: 404 pada detail produk
**Penyebab**: Product query filter tidak cocok dengan kondisi aktual
**Solusi**: Check apakah store.status = 'active' dan user.is_seller = true

---

## Kesimpulan

Test suite ini memverifikasi **seluruh alur bisnis utama** aplikasi Arradea:
- ✅ User dapat beralih role dari buyer ke seller
- ✅ Seller dapat upload produk
- ✅ Produk langsung terlihat oleh buyers lain
- ✅ Perubahan produk langsung ter-update
- ✅ Visibility rules diterapkan dengan benar
- ✅ Role-based access control berfungsi

**Dengan test ini lulus, alur core marketplace terjamin berfungsi dengan baik!** 🎉
