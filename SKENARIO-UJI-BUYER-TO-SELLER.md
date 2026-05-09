# 🎯 SKENARIO UJI: BUYER BERALIH MENJADI SELLER

## 📋 Ringkasan Eksekutif

Test ini memverifikasi skenario bisnis kritis:
- **Buyer** dapat beralih menjadi **Seller**
- **Seller** dapat mengunggah produk
- Produk **langsung terlihat** oleh buyers lain
- Semua operasi CRUD produk berfungsi dengan benar

---

## 🚀 Alur Skenario Utama

```
┌─────────────────────────────────────────────────────────────────┐
│                    BUYER (Pembeli Awal)                         │
│                    - is_seller = false                          │
│                    - Tidak punya toko                           │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
         [PATCH /api/profile/seller-mode]
         Body: { enable: true, store_name: "..." }
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                    SELLER (Berhasil Beralih)                    │
│                    - is_seller = true                           │
│                    - Punya toko dengan nama "Toko Elektronik"  │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
         [POST /api/products]
         Body: { name: "Smartphone XYZ", price: 4500000, ... }
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                  PRODUK (Berhasil Diupload)                     │
│                  - Tersimpan di database                        │
│                  - store_id = Toko Elektronik                   │
│                  - Stok: 15 unit                                │
└────────────────────┬────────────────────────────────────────────┘
                     │
      ┌──────────────┼──────────────┐
      │              │              │
      ▼              ▼              ▼
  [GET /api/products]  [GET /api/products/{id}]  [Search]
  (List semua)         (Detail produk)           (Cari produk)
      │              │              │
      └──────────────┼──────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│         VISIBLE TO OTHER BUYERS (Pembeli Lain)                  │
│         ✅ Produk terlihat di daftar publik                     │
│         ✅ Detail produk dapat diakses                          │
│         ✅ Produk dapat dicari                                  │
│         ✅ Informasi toko ditampilkan                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📝 Test Cases Lengkap

### Test 1️⃣: Main Workflow (Alur Utama)
**Nama**: `test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers`

**Verifikasi**:
```
✅ Buyer awal: is_seller = false, store = null
✅ Setelah toggle: is_seller = true, store dibuat
✅ Produk diupload: Rp 4.500.000, stok 15
✅ Terlihat di listing publik
✅ Buyer lain bisa lihat detail
✅ Produk bisa dicari
```

**Assertion Key**:
- Store name: "Toko Elektronik Budi"
- Produk name: "Smartphone XYZ"
- Product price: 4500000
- Product stock: 15

---

### Test 2️⃣: Multiple Products
**Nama**: `test_seller_uploads_multiple_products_all_visible_to_buyers`

**Produk yang diupload**:
1. Laptop Pro 15 - Rp 15.000.000 (stok 5)
2. Monitor 4K - Rp 3.500.000 (stok 8)
3. Keyboard Mechanical - Rp 1.200.000 (stok 20)

**Verifikasi**: Ketiga produk terlihat di listing dengan store.name yang konsisten

---

### Test 3️⃣: Product Modification
**Nama**: `test_seller_modifies_product_changes_visible_to_buyers`

**Skenario**:
```
BEFORE: T-Shirt Original, Rp 150.000, stok 50
  ↓ [PUT /api/products/{id}]
AFTER:  T-Shirt Premium Edition, Rp 250.000, stok 30
  ↓
✅ Buyer lain langsung melihat perubahan
```

---

### Test 4️⃣: Product Deletion
**Nama**: `test_seller_deletes_product_no_longer_visible_to_buyers`

**Skenario**:
```
Produk "Book: Laravel Guide" ada di listing
  ↓ [DELETE /api/products/{id}]
Produk dihapus dari database
  ↓
✅ Buyer lain mendapat 404 saat akses
```

---

### Test 5️⃣: Visibility Rules
**Nama**: `test_inactive_seller_products_not_visible_to_buyers`

**Verifikasi**:
```
Produk dari toko dengan status = 'inactive'
  ↓
❌ TIDAK terlihat di listing publik
❌ TIDAK bisa diakses langsung (404)

Filter yang berlaku:
- store.status = 'active'
- user.is_seller = true
```

---

### Test 6️⃣: Toggle Off (Kembali ke Buyer)
**Nama**: `test_seller_switches_back_to_buyer_products_remain`

**Skenario**:
```
Seller dengan produk
  ↓ [PATCH /api/profile/seller-mode { enable: false }]
Menjadi Buyer lagi
  ↓
✅ Produk tetap ada di database
✅ Produk tetap terlihat untuk buyers lain
✅ Tidak bisa membuat produk baru (403)
```

---

## 🔗 Endpoints yang Diuji

| No | HTTP | Endpoint | Auth | Role | Fungsi |
|---|------|----------|------|------|--------|
| 1 | PATCH | `/api/profile/seller-mode` | Ya | Buyer/Seller | Toggle mode |
| 2 | POST | `/api/products` | Ya | Seller | Buat produk |
| 3 | GET | `/api/products` | Tidak | Public | List produk |
| 4 | GET | `/api/products/{id}` | Tidak | Public | Detail produk |
| 5 | GET | `/api/products/search` | Tidak | Public | Cari produk |
| 6 | PUT | `/api/products/{id}` | Ya | Seller | Update produk |
| 7 | DELETE | `/api/products/{id}` | Ya | Seller | Hapus produk |

---

## 💾 Database Tables

Tables yang terlibat dalam test:

```
┌──────────────┐
│ users        │
├──────────────┤
│ id           │
│ is_seller    │ ← Diubah saat toggle
│ role         │
└──────────────┘
       │
       ├─ has_one ─────────────────┐
       │                           │
       ▼                           ▼
┌──────────────┐           ┌──────────────┐
│ stores       │           │ orders       │
├──────────────┤           ├──────────────┤
│ id           │           │ id           │
│ user_id      │           │ user_id      │
│ name         │           │ store_id     │
│ status       │ ◄─ aktif  │ product_id   │
└──────────────┘           └──────────────┘
       │
       └─ has_many
              │
              ▼
       ┌──────────────┐
       │ products     │
       ├──────────────┤
       │ id           │
       │ store_id     │ ◄─ Diisi saat create
       │ name         │
       │ price        │
       │ stock        │
       │ image        │
       └──────────────┘
```

---

## ⚙️ Setup & Configuration

**Location Config** (di config/location.php atau via test setUp):
```php
'center_lat' => -6.200000,      // Jakarta
'center_lng' => 106.816666,
'max_radius' => 5               // 5 km
```

**Access Code untuk test**:
```
Code: BUYER-SELLER-TEST
Status: active (dibuat di setUp())
```

**Database untuk test**:
```
Type: SQLite
Location: :memory: (in-memory, bersih setiap test)
```

---

## 🏃 Cara Menjalankan

### Minimal
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php
```

### Dengan Detail
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --verbose
```

### Test Spesifik
```bash
# Test hanya toggle dan upload
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php \
  --filter=test_buyer_switches_to_seller

# Test hanya visibility
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php \
  --filter=test_inactive_seller
```

### Dengan Report
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --coverage
```

---

## ✅ Expected Results

Ketika semua test lulus:
```
PASS  Tests\Feature\BuyerToSellerWorkflowTest
  ✓ test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers
  ✓ test_seller_uploads_multiple_products_all_visible_to_buyers
  ✓ test_seller_modifies_product_changes_visible_to_buyers
  ✓ test_seller_deletes_product_no_longer_visible_to_buyers
  ✓ test_inactive_seller_products_not_visible_to_buyers
  ✓ test_seller_switches_back_to_buyer_products_remain

Tests:  6 passed
Time:   ~5-10 seconds
```

---

## 🔍 Debugging

Jika test gagal, periksa:

| Error | Penyebab | Solusi |
|-------|----------|--------|
| `404 Not Found` | Produk tidak ada | Verifikasi create berhasil |
| `403 Forbidden` | Role tidak valid | Refresh user setelah toggle |
| `422 Unprocessable` | Validasi gagal | Check ProductRequest rules |
| `Assertion failed` | Data tidak match | Check value di response JSON |

---

## 📊 Checklist Verifikasi

- ✅ Test file: `tests/Feature/BuyerToSellerWorkflowTest.php` (445 lines)
- ✅ 6 test methods lengkap
- ✅ Setup dengan AccessCode dan 2 users
- ✅ Setiap test independent (RefreshDatabase)
- ✅ Menggunakan Sanctum authentication
- ✅ Response assertions comprehensive
- ✅ Database state verification
- ✅ Role-based access control tested

---

## 🎓 Key Learning Points

1. **Dynamic Role Switching** 🔄
   - User dapat toggle is_seller flag
   - Store otomatis dibuat saat toggle ON

2. **Product Lifecycle** 📦
   - Create → Visible → Update → Delete
   - Setiap perubahan instantly terlihat

3. **Visibility Filtering** 👁️
   - Query auto-filter by store.status dan is_seller
   - Inactive products tersembunyi

4. **Access Control** 🔐
   - Middleware role:seller menyeleksi berdasarkan is_seller
   - Buyers tidak bisa akses seller endpoints

---

## 📞 Support

Untuk informasi lebih lanjut, lihat:
- Test code: `tests/Feature/BuyerToSellerWorkflowTest.php`
- Documentation: `TEST-SCENARIO-BUYER-TO-SELLER.md`
- Source code:
  - Controller: `app/Http/Controllers/AuthController.php` (toggleSellerMode)
  - Controller: `app/Http/Controllers/ProductController.php` (CRUD)
  - Model: `app/Models/User.php`, `Store.php`, `Product.php`

---

**Status: ✅ READY TO RUN**

Run test sekarang dengan:
```bash
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --verbose
```
