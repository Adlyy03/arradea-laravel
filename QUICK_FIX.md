# ⚡ Quick Fix Reference

Solusi cepat untuk masalah umum di Arradea Marketplace.

---

## 🔥 Halaman Blank

```bash
# 1. Unregister service worker (paste di browser console)
navigator.serviceWorker.getRegistrations().then(r => r.forEach(x => x.unregister()));

# 2. Clear cache & rebuild
php artisan cache:clear && npm run dev

# 3. Hard reload browser
# Ctrl + Shift + R
```

---

## 🖼️ Gambar Tidak Muncul

```bash
# Option 1: Artisan Command (Recommended)
php artisan fix:images

# Option 2: Quick fix manual
php artisan storage:link && php artisan cache:clear && chmod -R 775 storage

# Option 3: Gunakan script
bash fix-images.sh        # Linux/Mac
.\fix-images.ps1          # Windows

# Check only (no changes)
php artisan fix:images --check
```

---

## 🔔 Firebase Error

```javascript
// Check status (paste di console)
console.log('FCM:', window.Arradea?.notification ? '✅' : '❌');
console.log('Permission:', Notification.permission);

// Manual request
window.Arradea.notification.request();
```

---

## 🗄️ Database Image Path Salah

```sql
-- Fix double /storage/
UPDATE products 
SET image = REPLACE(image, '/storage/storage/', '/storage/') 
WHERE image LIKE '/storage/storage/%';

-- Add missing /storage/
UPDATE products 
SET image = CONCAT('/storage/', image) 
WHERE image NOT LIKE 'http%' 
  AND image NOT LIKE '/storage/%'
  AND image IS NOT NULL;
```

---

## 🧹 Clear Everything

```bash
# Laravel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Frontend
npm run build

# Browser
# Ctrl + Shift + Delete → Clear cache
```

---

## 🧪 Quick Test

```bash
# Test storage link
ls -la public/storage

# Test image path
php artisan tinker
>>> \App\Models\Product::latest()->first()->image

# Test FCM (browser console)
window.Arradea.notification.isSupported()
```

---

## 📋 Setup Checklist

```bash
# 1. Storage
php artisan storage:link
mkdir -p storage/app/public/products

# 2. Permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# 3. Cache
php artisan cache:clear

# 4. Frontend
npm run dev
```

---

## 🚨 Emergency Reset

```bash
# Remove service worker
# Browser console:
navigator.serviceWorker.getRegistrations().then(r => r.forEach(x => x.unregister()));

# Reset storage link
rm -rf public/storage
php artisan storage:link

# Clear all
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild
npm run build
```

---

## 📚 Full Documentation

- `PERBAIKAN_SUMMARY.md` - Complete summary
- `FIREBASE_TROUBLESHOOTING.md` - Firebase issues
- `TROUBLESHOOTING_IMAGES.md` - Image issues
- `CONTOH_PENGGUNAAN_FCM.md` - FCM examples

---

**Need help?** Check the full documentation files above! 🚀
