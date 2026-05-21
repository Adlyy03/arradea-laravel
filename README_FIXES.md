# 🛠️ Arradea Marketplace - Fixes & Troubleshooting

Dokumentasi lengkap untuk troubleshooting dan perbaikan masalah umum di Arradea Marketplace.

---

## 📚 Dokumentasi yang Tersedia

| File | Deskripsi | Kapan Digunakan |
|------|-----------|-----------------|
| **QUICK_FIX.md** | Solusi cepat 1-liner | Butuh fix cepat tanpa baca dokumentasi panjang |
| **PERBAIKAN_SUMMARY.md** | Summary lengkap semua perbaikan | Ingin tahu apa saja yang sudah diperbaiki |
| **FIREBASE_TROUBLESHOOTING.md** | Troubleshooting Firebase FCM | Masalah dengan push notifications |
| **TROUBLESHOOTING_IMAGES.md** | Troubleshooting gambar produk | Gambar tidak muncul atau error upload |
| **CONTOH_PENGGUNAAN_FCM.md** | Contoh implementasi FCM | Ingin implementasi notifikasi di fitur baru |

---

## ⚡ Quick Start

### Masalah: Halaman Blank Setelah Setup Firebase

```bash
# 1. Unregister service worker (browser console)
navigator.serviceWorker.getRegistrations().then(r => r.forEach(x => x.unregister()));

# 2. Clear cache & rebuild
php artisan cache:clear && npm run dev

# 3. Hard reload: Ctrl + Shift + R
```

**Baca:** `FIREBASE_TROUBLESHOOTING.md`

---

### Masalah: Gambar Produk Tidak Muncul

```bash
# Quick fix dengan Artisan command
php artisan fix:images

# Atau manual
php artisan storage:link
php artisan cache:clear
chmod -R 775 storage
```

**Baca:** `TROUBLESHOOTING_IMAGES.md`

---

## 🔧 Tools yang Tersedia

### 1. Artisan Command

```bash
# Fix semua masalah gambar
php artisan fix:images

# Check masalah tanpa fix
php artisan fix:images --check
```

**Features:**
- ✅ Check & create storage link
- ✅ Create missing folders
- ✅ Fix permissions (Linux/Mac)
- ✅ Fix database image paths
- ✅ Clear cache

### 2. Shell Scripts

**Linux/Mac:**
```bash
bash fix-images.sh
```

**Windows:**
```powershell
.\fix-images.ps1
```

**Features:**
- ✅ Interactive prompts
- ✅ Step-by-step fixes
- ✅ Color-coded output
- ✅ Safety checks

---

## 📋 Common Issues & Solutions

### 1. Halaman Blank / White Screen

**Penyebab:** Firebase FCM error crash halaman

**Solusi:**
1. Unregister service worker
2. Clear browser cache
3. Hard reload (Ctrl + Shift + R)
4. Rebuild: `npm run dev`

**Detail:** `FIREBASE_TROUBLESHOOTING.md` → Issue 1

---

### 2. Gambar Tidak Muncul

**Penyebab:** Storage link belum dibuat atau path salah

**Solusi:**
```bash
php artisan fix:images
```

**Detail:** `TROUBLESHOOTING_IMAGES.md` → Issue 1

---

### 3. Service Worker Error

**Penyebab:** Service worker registration failed

**Solusi:**
1. Check HTTPS atau localhost
2. Check file `public/firebase-messaging-sw.js` ada
3. Unregister old service workers

**Detail:** `FIREBASE_TROUBLESHOOTING.md` → Issue 2

---

### 4. Permission Denied / Blocked

**Penyebab:** Notification permission di-block browser

**Solusi:**
1. Reset permission di browser settings
2. Clear site data
3. Request permission lagi

**Detail:** `FIREBASE_TROUBLESHOOTING.md` → Issue 3

---

### 5. FCM Token Tidak Didapat

**Penyebab:** VAPID key salah atau service worker error

**Solusi:**
1. Check VAPID key di `resources/js/firebase.js`
2. Re-register service worker
3. Check Firebase Console settings

**Detail:** `FIREBASE_TROUBLESHOOTING.md` → Issue 4

---

### 6. Gambar 404 Not Found

**Penyebab:** Storage link belum dibuat

**Solusi:**
```bash
php artisan storage:link
```

**Detail:** `TROUBLESHOOTING_IMAGES.md` → Issue 1

---

### 7. Gambar 403 Forbidden

**Penyebab:** Permission salah

**Solusi:**
```bash
chmod -R 775 storage
```

**Detail:** `TROUBLESHOOTING_IMAGES.md` → Issue 1

---

### 8. Double /storage/ Path

**Penyebab:** Database path salah

**Solusi:**
```bash
php artisan fix:images
```

Atau manual SQL:
```sql
UPDATE products 
SET image = REPLACE(image, '/storage/storage/', '/storage/') 
WHERE image LIKE '/storage/storage/%';
```

**Detail:** `TROUBLESHOOTING_IMAGES.md` → Issue 2

---

## 🧪 Testing

### Test Firebase FCM

```javascript
// Browser console
console.log('FCM:', window.Arradea?.notification ? '✅' : '❌');
console.log('Permission:', Notification.permission);
console.log('Supported:', window.Arradea?.notification?.isSupported());

// Request permission
window.Arradea.notification.request();
```

### Test Image Upload

1. Login sebagai seller
2. Buka `/seller/products/create`
3. Upload gambar
4. Submit
5. Check apakah muncul di list

### Test Storage Link

```bash
# Check link exists
ls -la public/storage

# Check target
readlink public/storage
# Should show: ../storage/app/public
```

### Test Image Path

```bash
php artisan tinker
>>> $product = \App\Models\Product::latest()->first();
>>> $product->image;
# Should return full URL like: http://localhost:8000/storage/products/xxx.jpg
```

---

## 🚀 Deployment Checklist

Sebelum deploy ke production:

### Pre-deployment
- [ ] Test semua fitur di local
- [ ] Run `npm run build` (production build)
- [ ] Update `.env` dengan production values
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set correct `APP_URL`

### Post-deployment
- [ ] Upload files ke server
- [ ] Run `php artisan storage:link`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set permissions: `chmod -R 775 storage bootstrap/cache`
- [ ] Test upload gambar
- [ ] Test Firebase FCM
- [ ] Check browser console untuk errors
- [ ] Test di multiple browsers

---

## 📞 Getting Help

### Step 1: Check Documentation
Baca file dokumentasi yang relevan:
- Halaman blank → `FIREBASE_TROUBLESHOOTING.md`
- Gambar error → `TROUBLESHOOTING_IMAGES.md`
- Butuh contoh → `CONTOH_PENGGUNAAN_FCM.md`

### Step 2: Run Diagnostic Tools
```bash
# Check images
php artisan fix:images --check

# Check logs
tail -f storage/logs/laravel.log
```

### Step 3: Check Browser Console
1. Open DevTools (F12)
2. Check Console tab untuk errors
3. Check Network tab untuk failed requests
4. Check Application tab → Service Workers

### Step 4: Try Quick Fixes
```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Fix images
php artisan fix:images

# Rebuild frontend
npm run build
```

---

## 🔄 Update History

### May 21, 2026
- ✅ Fixed blank page after Firebase FCM setup
- ✅ Fixed product images not displaying
- ✅ Added comprehensive error handling
- ✅ Created troubleshooting documentation
- ✅ Created auto-fix tools (artisan command + scripts)
- ✅ Improved service worker implementation
- ✅ Enhanced model image accessor

---

## 📁 File Structure

```
arradea-marketplace/
├── app/
│   └── Console/Commands/
│       └── FixImagesCommand.php         ← Artisan command
├── resources/js/
│   ├── app.js                           ← Fixed FCM init
│   └── firebase.js                      ← Fixed error handling
├── public/
│   └── firebase-messaging-sw.js         ← Fixed fetch handler
├── QUICK_FIX.md                         ← Quick reference
├── PERBAIKAN_SUMMARY.md                 ← Complete summary
├── FIREBASE_TROUBLESHOOTING.md          ← Firebase guide
├── TROUBLESHOOTING_IMAGES.md            ← Images guide
├── CONTOH_PENGGUNAAN_FCM.md             ← FCM examples
├── README_FIXES.md                      ← This file
├── fix-images.sh                        ← Linux/Mac script
└── fix-images.ps1                       ← Windows script
```

---

## 💡 Best Practices

### Development
1. ✅ Always test in local before deploy
2. ✅ Use `npm run dev` for development
3. ✅ Check browser console regularly
4. ✅ Clear cache after code changes
5. ✅ Test in multiple browsers

### Firebase FCM
1. ✅ Wrap all Firebase code in try/catch
2. ✅ Check browser support before init
3. ✅ Provide fallbacks if FCM fails
4. ✅ Don't block main thread
5. ✅ Log errors for debugging

### Image Handling
1. ✅ Always use storage link
2. ✅ Store relative paths in database
3. ✅ Use model accessors for URLs
4. ✅ Validate uploads
5. ✅ Provide fallback placeholders

### Production
1. ✅ Use `npm run build` (not dev)
2. ✅ Set `APP_DEBUG=false`
3. ✅ Cache configs and routes
4. ✅ Set proper permissions
5. ✅ Monitor logs regularly

---

## 🎯 Quick Commands Reference

```bash
# Fix images
php artisan fix:images

# Check only
php artisan fix:images --check

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Storage link
php artisan storage:link

# Build frontend
npm run dev          # Development
npm run build        # Production

# Check logs
tail -f storage/logs/laravel.log

# Tinker
php artisan tinker
>>> \App\Models\Product::latest()->first()->image
```

---

**Status:** ✅ All issues resolved

**Last Updated:** May 21, 2026

**Happy Coding! 🚀**
