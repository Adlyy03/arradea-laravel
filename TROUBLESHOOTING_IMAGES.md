# 🖼️ Troubleshooting Gambar Produk

Panduan lengkap untuk mengatasi masalah gambar produk tidak muncul di Arradea Marketplace.

---

## ✅ Quick Fix Checklist

Jalankan perintah ini secara berurutan:

```bash
# Option 1: Gunakan Artisan Command (Recommended)
php artisan fix:images

# Option 2: Manual Commands
# 1. Buat symbolic link storage
php artisan storage:link

# 2. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 3. Pastikan folder storage writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4. Rebuild frontend
npm run build

# Option 3: Gunakan Script
bash fix-images.sh        # Linux/Mac
.\fix-images.ps1          # Windows
```

### Check Only (Tanpa Mengubah)

```bash
# Check masalah tanpa fix
php artisan fix:images --check
```

---

## 🔍 Diagnosa Masalah

### 1. Check Storage Link

Storage link menghubungkan `public/storage` ke `storage/app/public`.

**Check apakah link sudah ada:**

```bash
# Windows (PowerShell)
Test-Path public/storage

# Linux/Mac
ls -la public/storage
```

**Jika belum ada, buat link:**

```bash
php artisan storage:link
```

**Output yang benar:**
```
The [public/storage] link has been connected to [storage/app/public].
The links have been created.
```

**Jika error "link already exists":**

```bash
# Windows (PowerShell as Admin)
Remove-Item public/storage -Force
php artisan storage:link

# Linux/Mac
rm -rf public/storage
php artisan storage:link
```

---

### 2. Check Folder Structure

Pastikan struktur folder benar:

```
storage/
├── app/
│   ├── public/          ← Gambar disimpan di sini
│   │   ├── products/    ← Gambar produk
│   │   ├── categories/  ← Gambar kategori
│   │   └── payments/    ← Bukti pembayaran
│   └── ...
└── ...

public/
├── storage/             ← Symbolic link ke storage/app/public
└── ...
```

**Check folder products ada:**

```bash
# Windows
Test-Path storage/app/public/products

# Linux/Mac
ls -la storage/app/public/products
```

**Jika belum ada, buat folder:**

```bash
mkdir -p storage/app/public/products
mkdir -p storage/app/public/categories
mkdir -p storage/app/public/payments
```

---

### 3. Check File Permissions

**Linux/Mac:**

```bash
# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (optional, sesuaikan dengan user web server)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

**Windows:**

- Klik kanan folder `storage` → Properties → Security
- Pastikan user Anda punya Full Control

---

### 4. Check Database

Gambar produk disimpan di database sebagai path relatif.

**Check format path di database:**

```sql
-- Jalankan di MySQL/phpMyAdmin
SELECT id, name, image FROM products LIMIT 5;
```

**Format yang benar:**

```
✅ /storage/products/1234567890_image.jpg
✅ https://images.unsplash.com/photo-xxx
❌ storage/products/image.jpg (missing leading slash)
❌ /storage/storage/products/image.jpg (double storage)
```

**Jika format salah, perbaiki:**

```sql
-- Tambahkan leading slash jika hilang
UPDATE products 
SET image = CONCAT('/storage/', image) 
WHERE image NOT LIKE 'http%' 
  AND image NOT LIKE '/storage/%'
  AND image IS NOT NULL;

-- Hapus double /storage/
UPDATE products 
SET image = REPLACE(image, '/storage/storage/', '/storage/') 
WHERE image LIKE '/storage/storage/%';
```

---

### 5. Check Browser Console

Buka DevTools (F12) → Console, cari error:

**Error: 404 Not Found**
```
GET http://localhost:8000/storage/products/image.jpg 404
```

**Solusi:**
- Storage link belum dibuat → `php artisan storage:link`
- File tidak ada di `storage/app/public/products/`

**Error: 403 Forbidden**
```
GET http://localhost:8000/storage/products/image.jpg 403
```

**Solusi:**
- Permission salah → `chmod -R 775 storage`

**Error: Mixed Content (HTTPS/HTTP)**
```
Mixed Content: The page at 'https://...' was loaded over HTTPS, 
but requested an insecure image 'http://...'.
```

**Solusi:**
- Update `.env`: `APP_URL=https://yourdomain.com`
- Clear config: `php artisan config:clear`

---

## 🐛 Common Issues

### Issue 1: Gambar Tidak Muncul Setelah Upload

**Gejala:**
- Upload berhasil
- Tidak ada error
- Tapi gambar tidak tampil

**Penyebab:**
- Storage link belum dibuat
- Path di database salah

**Solusi:**

```bash
# 1. Buat storage link
php artisan storage:link

# 2. Check path di database
php artisan tinker
>>> \App\Models\Product::latest()->first()->image
# Should return: "/storage/products/xxx.jpg" or "https://..."

# 3. Clear cache
php artisan cache:clear
```

---

### Issue 2: Gambar Lama Hilang Setelah Update Code

**Gejala:**
- Gambar yang sudah ada tiba-tiba tidak muncul
- Setelah update model atau controller

**Penyebab:**
- Accessor model berubah
- Path format berubah

**Solusi:**

```bash
# 1. Check format path di database
php artisan tinker
>>> \App\Models\Product::find(1)->getAttributes()['image']
# Lihat raw value dari database

# 2. Jika format salah, perbaiki dengan SQL di atas

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
```

---

### Issue 3: Gambar Muncul di Local Tapi Tidak di Production

**Gejala:**
- Di localhost gambar muncul
- Di server production tidak muncul

**Penyebab:**
- Storage link belum dibuat di server
- Permission salah di server
- APP_URL salah

**Solusi:**

```bash
# SSH ke server, lalu:

# 1. Buat storage link
php artisan storage:link

# 2. Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 3. Check APP_URL di .env
cat .env | grep APP_URL
# Should be: APP_URL=https://yourdomain.com

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
```

---

### Issue 4: Service Worker Memblokir Gambar

**Gejala:**
- Gambar tidak muncul setelah setup Firebase FCM
- Console menunjukkan request di-intercept

**Penyebab:**
- Service worker mengintercept fetch requests

**Solusi:**

Sudah diperbaiki di `public/firebase-messaging-sw.js`:

```javascript
// IMPORTANT: Let all fetch requests pass through
self.addEventListener('fetch', (event) => {
    // Pass through - don't intercept
    return;
});
```

**Jika masih bermasalah:**

```javascript
// Paste di browser console
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
});
// Reload halaman
```

---

### Issue 5: Gambar Placeholder Tidak Muncul

**Gejala:**
- Produk tanpa gambar menampilkan broken image
- Placeholder Unsplash tidak load

**Penyebab:**
- Internet connection issue
- Unsplash blocked

**Solusi:**

Ganti placeholder di `app/Models/Product.php`:

```php
public function getImageAttribute($value)
{
    if (!$value) {
        // Option 1: Local placeholder
        return asset('images/placeholder-product.png');
        
        // Option 2: Data URI (base64)
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+';
        
        // Option 3: Unsplash (current)
        return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&h=500';
    }
    
    // ... rest of code
}
```

---

## 🧪 Testing

### Test 1: Upload Gambar Baru

1. Login sebagai seller
2. Buka `/seller/products/create`
3. Upload gambar produk
4. Submit form
5. Check apakah gambar muncul di list produk

**Expected:**
- Upload berhasil
- Gambar muncul di `/seller/products`
- File ada di `storage/app/public/products/`

### Test 2: Check Storage Link

```bash
# Windows (PowerShell)
Get-Item public/storage | Select-Object Target

# Linux/Mac
ls -la public/storage
```

**Expected:**
```
public/storage -> ../storage/app/public
```

### Test 3: Check Image URL

```bash
php artisan tinker
>>> $product = \App\Models\Product::latest()->first();
>>> $product->image;
```

**Expected:**
```
"http://localhost:8000/storage/products/1234567890_image.jpg"
```

### Test 4: Manual Access

Buka browser, akses langsung:
```
http://localhost:8000/storage/products/1234567890_image.jpg
```

**Expected:**
- Gambar muncul
- Tidak 404 atau 403

---

## 📋 Debug Commands

```bash
# Check storage link
php artisan storage:link

# List files in storage
ls -la storage/app/public/products/

# Check latest product image
php artisan tinker
>>> \App\Models\Product::latest()->first()->image

# Check raw database value
>>> \App\Models\Product::latest()->first()->getAttributes()['image']

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Rebuild assets
npm run build
```

---

## 🚨 Emergency: Reset Storage

Jika semua cara gagal, reset storage:

```bash
# BACKUP DULU!
cp -r storage/app/public storage_backup

# Remove storage link
rm -rf public/storage

# Recreate storage link
php artisan storage:link

# Restore files
cp -r storage_backup/* storage/app/public/

# Set permissions
chmod -R 775 storage
```

---

## 📚 File Locations

**Model Accessor:**
- `app/Models/Product.php` → `getImageAttribute()`

**Upload Handler:**
- `app/Http/Controllers/ProductWebController.php` → `store()` & `update()`

**Storage Config:**
- `config/filesystems.php` → `'public'` disk

**Views:**
- `resources/views/seller/products/index.blade.php`
- `resources/views/seller/products/create.blade.php`
- `resources/views/welcome.blade.php`

---

## 💡 Best Practices

1. **Selalu gunakan storage link** untuk file uploads
2. **Simpan path relatif** di database (`/storage/products/xxx.jpg`)
3. **Gunakan accessor** di model untuk convert ke full URL
4. **Validate file upload** (size, type, dimensions)
5. **Optimize images** sebelum save (resize, compress)
6. **Backup storage folder** secara berkala
7. **Set proper permissions** di production

---

**Last Updated:** May 21, 2026
