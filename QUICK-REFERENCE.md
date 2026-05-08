# Quick Reference: Test Skenario Buyer → Seller

## 📁 File yang Dibuat

```
📂 Laravel Project Root
├── 📄 tests/Feature/BuyerToSellerWorkflowTest.php    ← TEST UTAMA (445 lines)
├── 📄 TEST-SCENARIO-BUYER-TO-SELLER.md              ← Dokumentasi teknis
├── 📄 SKENARIO-UJI-BUYER-TO-SELLER.md               ← Panduan visual (Bahasa Indonesia)
└── 📄 SESSION: TEST-DELIVERY-SUMMARY.md             ← Summary delivery
```

---

## 🚀 Quick Start

```bash
# 1. Buka terminal di folder project
cd c:\laragon\www\arradeaaaa

# 2. Run test
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --verbose

# 3. Lihat hasil ✅
```

---

## 📝 6 Test Methods

| # | Test Name | Deskripsi |
|---|-----------|-----------|
| 1️⃣ | `test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers` | **Main flow**: Toggle → Upload → Visibility |
| 2️⃣ | `test_seller_uploads_multiple_products_all_visible_to_buyers` | 3 produk dari 1 seller, semua terlihat |
| 3️⃣ | `test_seller_modifies_product_changes_visible_to_buyers` | Update produk, perubahan instant terlihat |
| 4️⃣ | `test_seller_deletes_product_no_longer_visible_to_buyers` | Delete produk, tidak terlihat lagi |
| 5️⃣ | `test_inactive_seller_products_not_visible_to_buyers` | Filter visibility: hanya toko aktif |
| 6️⃣ | `test_seller_switches_back_to_buyer_products_remain` | Toggle off, produk tetap ada |

---

## 🔄 Workflow Diagram

```
Buyer (is_seller=false) 
   ↓ PATCH /api/profile/seller-mode
Seller (is_seller=true) 
   ↓ POST /api/products
Product uploaded & in DB
   ↓ GET /api/products
Visible to all buyers ✅
```

---

## ✔️ Verifikasi Utama

- ✅ User dapat toggle role buyer ↔ seller
- ✅ Store otomatis dibuat saat toggle ON
- ✅ Seller dapat upload produk
- ✅ Produk **LANGSUNG TERLIHAT** di listing publik
- ✅ Buyer lain bisa lihat detail produk
- ✅ Produk dapat dicari
- ✅ Update produk instant ter-update
- ✅ Delete produk hilang dari listing
- ✅ Inactive store products tidak terlihat
- ✅ Role-based access control berjalan

---

## 🎯 Endpoints Tested

```
Authentication:
  PATCH /api/profile/seller-mode          ← Toggle mode

Seller Operations:
  POST   /api/products                    ← Create
  PUT    /api/products/{id}               ← Update
  DELETE /api/products/{id}               ← Delete

Public Endpoints:
  GET    /api/products                    ← List
  GET    /api/products/{id}               ← Detail
  GET    /api/products/search?q=...       ← Search
```

---

## 📊 Test Statistics

- **Total Tests**: 6
- **Total Assertions**: 50+
- **Code Coverage**: Full workflow
- **Lines of Code**: 445

---

## 💡 Key Points

1. **RefreshDatabase** - Setiap test bersih (SQLite in-memory)
2. **Sanctum Auth** - Menggunakan token-based auth
3. **Factory Pattern** - User created via factory
4. **JSON Assertions** - Verifikasi response JSON
5. **Database Assertions** - Verifikasi state di DB

---

## 🔧 Setup di Test

```php
// AccessCode dibuat otomatis
AccessCode::create([
    'code' => 'BUYER-SELLER-TEST',
    'is_active' => true
])

// 2 Buyers dibuat di setUp()
$buyer = User::factory()->create([
    'is_seller' => false,
    'role' => 'buyer'
])

$otherBuyer = User::factory()->create([
    'is_seller' => false,
    'role' => 'buyer'
])

// Location config
config([
    'location.center_lat' => -6.200000,
    'location.center_lng' => 106.816666,
    'location.max_radius' => 5,
])
```

---

## 🎓 What You'll Learn

Dari test ini, Anda akan memahami:

✅ Bagaimana user beralih role di Laravel  
✅ Bagaimana middleware role-based bekerja  
✅ Bagaimana filtering query dengan relations bekerja  
✅ Bagaimana cache invalidation bekerja  
✅ Bagaimana testing dengan factory & seeders  
✅ Bagaimana Sanctum authentication dalam test  

---

## 📚 Documentation Files

1. **`TEST-SCENARIO-BUYER-TO-SELLER.md`**
   - Detailed technical breakdown
   - Step-by-step execution
   - Troubleshooting guide

2. **`SKENARIO-UJI-BUYER-TO-SELLER.md`** 
   - Visual flow diagrams
   - Indonesian explanation
   - Complete rundown of all 6 tests

3. **`SKENARIO-UJI-BUYER-TO-SELLER.md`** (this file)
   - Quick reference
   - Fast lookup

---

## 🏃 Run Commands

```bash
# Run all tests
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php

# Run with details
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --verbose

# Run specific test
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php \
  --filter=test_buyer_switches_to_seller

# Run multiple tests
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php \
  --filter="test_buyer_switches|test_seller_uploads_multiple"

# Coverage report
php artisan test tests/Feature/BuyerToSellerWorkflowTest.php --coverage
```

---

## ✅ Expected Output

```
Tests\Feature\BuyerToSellerWorkflowTest
  ✓ test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers
  ✓ test_seller_uploads_multiple_products_all_visible_to_buyers
  ✓ test_seller_modifies_product_changes_visible_to_buyers
  ✓ test_seller_deletes_product_no_longer_visible_to_buyers
  ✓ test_inactive_seller_products_not_visible_to_buyers
  ✓ test_seller_switches_back_to_buyer_products_remain

Tests:  6 passed (xx.xx seconds)
```

---

## 🚨 If Test Fails

| Error | Fix |
|-------|-----|
| Product not visible | Verify store.status='active' & is_seller=true |
| 403 Forbidden | Refresh user after toggle: `$user->fresh()` |
| 404 Not Found | Check product query filters in ProductController |
| AssertionError | Verify assertion values match response JSON |

---

## 📞 Need Help?

Check:
- `TEST-SCENARIO-BUYER-TO-SELLER.md` - Full technical docs
- `SKENARIO-UJI-BUYER-TO-SELLER.md` - Visual guide & Indonesian
- Source: `app/Http/Controllers/AuthController.php` - toggleSellerMode()
- Source: `app/Http/Controllers/ProductController.php` - CRUD logic

---

## ✨ Summary

✅ **Test file created**: `tests/Feature/BuyerToSellerWorkflowTest.php`

✅ **Complete scenario**: Buyer → Seller → Product Upload → Visibility

✅ **6 comprehensive tests**: All aspects covered

✅ **Full documentation**: 3 detailed docs in Indonesian & English

✅ **Ready to run**: Just execute `php artisan test tests/Feature/BuyerToSellerWorkflowTest.php`

---

**Status: 🎉 COMPLETE & READY**
