# 👨‍💼 Admin Dummy Accounts

## Akun Admin yang Tersedia

### ✅ Admin Arradea (Default)
**File yang generate:** `database/seeders/DatabaseSeeder.php` (line 32-42)

```
Phone:    081200009999
Password: password
Role:     admin
Name:     Admin Arradea
Wilayah:  Arradea
```

**Status:** Sudah ada di database jika sudah run migration + seeding

---

## Cara Membuat Admin Baru

### Option 1: Menggunakan Script PHP (PALING GAMPANG) ⭐

**Buat admin dari nomor yang ada (update existing user):**
```bash
php make_admin.php 0895321217645
```

**Atau buat admin baru:**
```bash
php make_admin.php 08123456789
```

**Fitur:**
- ✅ Instant jadi admin (tanpa persetujuan admin lain)
- ✅ Auto-verify phone
- ✅ Include lokasi dari .env
- ✅ Bisa update user yang sudah ada

### Option 2: Menggunakan Seeder

1. Jalankan seeder yang sudah ada:
   ```bash
   php artisan db:seed --class=DatabaseSeeder
   ```
   Atau khusus admin:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

2. Admin akan tercreate otomatis dengan kredensial di atas.

### Option 3: Menggunakan Laravel Tinker

```bash
php artisan tinker

# Di dalam tinker shell:
>>> use App\Models\User;
>>> User::create([
>>>   'name' => 'Admin Baru',
>>>   'phone' => '08123456789',
>>>   'password' => bcrypt('admin123'),
>>>   'role' => 'admin',
>>>   'is_seller' => false,
>>>   'phone_verified_at' => now(),
>>>   'wilayah' => 'Jakarta',
>>> ]);
```

### Option 4: SQL Direct Insert

Gunakan file: `admin_dummy_account.sql`

---

## Field Struktur Tabel Users

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `id` | bigint | - | Primary key |
| `name` | varchar | - | Nama user |
| `phone` | varchar | - | **UNIQUE** |
| `phone_verified_at` | timestamp | null | Harus filled agar bisa login |
| `wilayah` | varchar | null | Lokasi/wilayah |
| `latitude` | float | null | Untuk map features |
| `longitude` | float | null | Untuk map features |
| `access_code_id` | bigint | null | FK to access_codes |
| `password` | varchar | - | Hashed dengan bcrypt |
| `is_seller` | boolean | false | Flag untuk seller |
| `seller_status` | varchar | null | none, pending, approved, rejected |
| `seller_applied_at` | timestamp | null | Waktu apply jadi seller |
| `seller_approved_at` | timestamp | null | Waktu approve jadi seller |
| `seller_rejected_at` | timestamp | null | Waktu reject seller |
| `seller_rejection_reason` | text | null | Alasan reject |
| `seller_otp_verified` | boolean | false | OTP verification flag |
| `store_status` | varchar | null | active, inactive, etc |
| `open_time` | time | null | Jam buka toko |
| `close_time` | time | null | Jam tutup toko |
| `auto_schedule` | boolean | false | Auto on/off store |
| `role` | enum | buyer | ENUM: admin, seller, buyer |
| `remember_token` | varchar | null | Session token |
| `created_at` | timestamp | now | |
| `updated_at` | timestamp | now | |

## Field Wajib untuk Admin User

```php
// Minimum required untuk create admin:
User::create([
    'name' => 'Admin Arradea',
    'phone' => '081200009999',              // UNIQUE
    'password' => bcrypt('password'),      // HASHED
    'role' => 'admin',                     // ENUM
    'is_seller' => false,                  // Always false untuk admin
    'phone_verified_at' => now(),          // REQUIRED untuk login
    'wilayah' => 'Arradea',                // Optional tapi recommended
    'latitude' => -6.5723514245397086,     // Lokasi default Jakarta
    'longitude' => 106.77478524708685,     // Lokasi default Jakarta
]);
```

---

## Login Flow untuk Admin

1. Pergi ke `/login`
2. Input phone: `081200009999`
3. Input password: `password`
4. Setelah login, akan redirect ke `/admin/dashboard`

---

## Test Users yang Juga Ada

```
🛒 Buyer
Phone: 081300000001
Name: Ahmad Rahman
Password: password

👨‍💼 Seller (dari SellerSeeder)
Phone: 081100000001
Name: Seller Toko 1
Password: password
```

---

## Catatan Penting

- ⚠️ Password yang ditampilkan di sini adalah **CONTOH SAJA** untuk development
- 🔒 Untuk production, **GANTI PASSWORD** dengan yang lebih aman
- 🗝️ Hash password menggunakan: `bcrypt()` (Laravel default)
- 📱 Phone harus **unique** di database
- ✅ Phone harus **verified** (`phone_verified_at` tidak null) agar bisa login
