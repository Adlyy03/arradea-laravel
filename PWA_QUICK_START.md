# 🚀 PWA Quick Start Guide - Arradea Marketplace

> Panduan cepat untuk mulai implementasi PWA dalam 1-2 hari

---

## ⚡ QUICK WINS (2-4 Jam)

### 1. Validasi Icon Files (30 menit)

```bash
# Check apakah semua icon ada
ls -la public/images/icons/

# Yang harus ada:
# icon-72x72.png, icon-96x96.png, icon-128x128.png, 
# icon-144x144.png, icon-152x152.png, icon-192x192.png,
# icon-384x384.png, icon-512x512.png
```

**Jika ada yang missing:**
1. Buka https://realfavicongenerator.net/
2. Upload logo Arradea
3. Download semua sizes
4. Extract ke `public/images/icons/`

### 2. Fix Install Button (1 jam)

**File**: `resources/js/pwa-install.js` (CREATE NEW)

```javascript
// Simple implementation
let deferredPrompt;
const installBtn = document.getElementById('pwa-install-btn');

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  if (installBtn) installBtn.style.display = 'flex';
});

if (installBtn) {
  installBtn.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`Install: ${outcome}`);
    deferredPrompt = null;
    installBtn.style.display = 'none';
  });
}

// Hide if already installed
if (window.matchMedia('(display-mode: standalone)').matches) {
  if (installBtn) installBtn.style.display = 'none';
}
```

**Import di** `resources/js/app.js`:
```javascript
import './pwa-install';
```

**Build**:
```bash
npm run build
```

### 3. Improve Service Worker (1-2 jam)

**Backup dulu**:
```bash
cp public/sw.js public/sw.js.backup
```

**Update** `public/sw.js` - Tambahkan di bagian atas:

```javascript
const CACHE_VERSION = '1.0.1'; // Increment setiap update
const CACHE_NAME = `arradea-v${CACHE_VERSION}`;
const CACHE_LIMIT = 50; // Max items in cache

// Assets yang HARUS di-cache
const STATIC_CACHE = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
];
```

**Update activate event** untuk cleanup old caches:

```javascript
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});
```

### 4. Better Offline Page (30 menit)

**Create**: `public/offline.html`

```html
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline - Arradea</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
      background: linear-gradient(135deg, #f2f5f2 0%, #e8f5e9 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .container {
      text-align: center;
      max-width: 400px;
    }
    .icon {
      width: 120px;
      height: 120px;
      margin: 0 auto 24px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    .icon svg {
      width: 60px;
      height: 60px;
      color: #72bf77;
    }
    h1 {
      font-size: 24px;
      font-weight: 800;
      color: #1e5128;
      margin-bottom: 12px;
    }
    p {
      font-size: 16px;
      color: #666;
      margin-bottom: 24px;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      padding: 12px 32px;
      background: linear-gradient(135deg, #72bf77, #4db85a);
      color: white;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      box-shadow: 0 4px 16px rgba(114, 191, 119, 0.3);
      transition: transform 0.2s;
    }
    .btn:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"/>
      </svg>
    </div>
    <h1>Anda Sedang Offline</h1>
    <p>Sepertinya koneksi internet Anda terputus. Beberapa fitur mungkin tidak tersedia.</p>
    <a href="/" class="btn" onclick="window.location.reload(); return false;">
      Coba Lagi
    </a>
  </div>
</body>
</html>
```

---

## 🧪 TESTING (30 menit)

### 1. Test Install Prompt

1. Buka Chrome (Desktop atau Android)
2. Navigate ke `https://your-domain.com`
3. Tunggu beberapa detik
4. Tombol "Install App" harus muncul
5. Klik dan install
6. App harus buka di standalone mode

### 2. Test Offline Mode

1. Buka Chrome DevTools (F12)
2. Tab **Application** → **Service Workers**
3. Centang **Offline**
4. Navigate ke halaman baru
5. Harus muncul offline page

### 3. Test Service Worker Update

1. Ubah `CACHE_VERSION` di `sw.js` (misal jadi `1.0.2`)
2. Reload page
3. DevTools → Application → Service Workers
4. Klik **Update**
5. Tidak boleh ada error

### 4. Run Lighthouse

1. Chrome DevTools → **Lighthouse**
2. Pilih **Progressive Web App**
3. Click **Generate report**
4. Target: **Score > 90**

**Common Issues**:
- ❌ Icons not found → Check file paths
- ❌ Manifest errors → Validate JSON syntax
- ❌ SW not registered → Check console errors
- ❌ HTTPS required → Use ngrok for local testing

---

## 📱 TESTING ON MOBILE

### Android (Chrome)

1. Deploy ke server dengan HTTPS
2. Buka di Chrome Android
3. Menu → **Install app** atau **Add to Home Screen**
4. Icon harus muncul di home screen
5. Buka app → harus standalone (no browser UI)

### iOS (Safari)

1. Buka di Safari iOS
2. Tap **Share** button
3. **Add to Home Screen**
4. Icon harus muncul
5. Buka app → harus standalone

**Note**: iOS tidak support install prompt, hanya manual add to home screen.

---

## 🎯 VALIDATION CHECKLIST

Setelah implementasi, pastikan semua ini ✅:

### Manifest
- [ ] File `manifest.json` valid (no JSON errors)
- [ ] All icon files exist dan accessible
- [ ] `start_url` correct
- [ ] `theme_color` dan `background_color` set
- [ ] `display: standalone` set

### Service Worker
- [ ] Registered successfully (check console)
- [ ] No errors di DevTools → Application → Service Workers
- [ ] Cache storage ada dan terisi
- [ ] Offline page works

### Install
- [ ] Install button muncul (desktop/Android)
- [ ] Install process works
- [ ] App buka di standalone mode
- [ ] Icon correct di home screen/taskbar

### Meta Tags
- [ ] `<link rel="manifest">` ada di semua pages
- [ ] `<meta name="theme-color">` ada
- [ ] Apple touch icons ada (untuk iOS)

### HTTPS
- [ ] Site accessible via HTTPS
- [ ] No mixed content warnings
- [ ] SSL certificate valid

---

## 🐛 COMMON ISSUES & QUICK FIXES

### Issue: Install button tidak muncul

**Causes**:
- Belum HTTPS
- Manifest invalid
- Service Worker error
- Belum visit 2x dengan gap 5 menit

**Fix**:
```javascript
// Debug di console
navigator.serviceWorker.getRegistration().then(reg => {
  console.log('SW registered:', !!reg);
});

// Check manifest
fetch('/manifest.json').then(r => r.json()).then(console.log);
```

### Issue: Service Worker tidak update

**Fix**:
```javascript
// Force update
navigator.serviceWorker.getRegistrations().then(regs => {
  regs.forEach(reg => reg.update());
});

// Or hard reload
// Ctrl + Shift + R (Windows/Linux)
// Cmd + Shift + R (Mac)
```

### Issue: Offline page tidak muncul

**Fix**:
1. Check `offline.html` ada di `/public/`
2. Check service worker cache `offline.html`
3. Check fetch handler di SW

```javascript
// Di sw.js fetch event
if (event.request.mode === 'navigate') {
  return caches.match('/offline.html');
}
```

### Issue: Icons tidak muncul

**Fix**:
1. Check file paths di manifest.json
2. Check MIME type: `image/png`
3. Check file permissions (readable)
4. Clear cache dan reload

```bash
# Check files
ls -la public/images/icons/

# Check MIME type di response headers
curl -I https://your-domain.com/images/icons/icon-192x192.png
```

---

## 📊 LIGHTHOUSE SCORE TARGETS

| Category | Target | Priority |
|----------|--------|----------|
| PWA | > 90 | 🔴 Critical |
| Performance | > 85 | 🟡 High |
| Accessibility | > 90 | 🟡 High |
| Best Practices | > 90 | 🟢 Medium |
| SEO | > 90 | 🟢 Medium |

**PWA Checklist Items**:
- ✅ Registers a service worker
- ✅ Responds with 200 when offline
- ✅ Has a web app manifest
- ✅ Uses HTTPS
- ✅ Redirects HTTP to HTTPS
- ✅ Has a viewport meta tag
- ✅ Contains icons for add to home screen
- ✅ Configured for a custom splash screen
- ✅ Sets a theme color

---

## 🚀 DEPLOYMENT

### Development
```bash
# Build assets
npm run build

# Test locally dengan HTTPS
# Option 1: Laravel Valet (Mac)
valet secure arradea

# Option 2: ngrok
ngrok http 8000

# Option 3: Laravel Sail dengan HTTPS
# Update docker-compose.yml
```

### Staging
```bash
# Deploy ke staging server
git push staging main

# Verify:
# 1. HTTPS working
# 2. Service worker registered
# 3. Manifest accessible
# 4. Icons loading
# 5. Install prompt works
```

### Production
```bash
# Backup dulu
php artisan backup:run

# Deploy
git push production main

# Post-deploy checks:
# 1. Run Lighthouse audit
# 2. Test install on mobile
# 3. Test offline mode
# 4. Monitor error logs
# 5. Check analytics
```

---

## 📈 MONITORING

### Metrics to Track

**Technical**:
- Service Worker registration rate
- Install prompt acceptance rate
- Offline page views
- Cache hit rate
- Service Worker errors

**User**:
- PWA install rate
- PWA vs web usage
- Retention rate (PWA users)
- Session duration (PWA vs web)

**Business**:
- Conversion rate (PWA vs web)
- Cart abandonment (PWA vs web)
- Order completion rate

### Tools

1. **Google Analytics**
```javascript
// Track PWA usage
if (window.matchMedia('(display-mode: standalone)').matches) {
  gtag('event', 'pwa_launch');
}
```

2. **Chrome DevTools**
- Application tab → Service Workers
- Application tab → Cache Storage
- Network tab → Offline testing

3. **Lighthouse CI**
```bash
npm install -g @lhci/cli
lhci autorun --collect.url=https://your-domain.com
```

---

## 🎓 NEXT STEPS

Setelah quick wins ini selesai:

1. **Week 2-3**: Implement background sync untuk offline orders
2. **Week 4**: Setup push notifications
3. **Week 5**: Image optimization & lazy loading
4. **Week 6**: Performance monitoring & analytics

Lihat `PWA_UPGRADE_PLAN.md` untuk roadmap lengkap.

---

## 💡 PRO TIPS

1. **Always increment cache version** saat deploy
   ```javascript
   const CACHE_VERSION = '1.0.2'; // Update ini!
   ```

2. **Test offline mode regularly**
   - DevTools → Network → Offline
   - Atau throttle ke "Slow 3G"

3. **Monitor service worker errors**
   ```javascript
   navigator.serviceWorker.addEventListener('error', (error) => {
     console.error('SW Error:', error);
     // Send to error tracking service
   });
   ```

4. **Use Chrome DevTools Application tab**
   - Best tool untuk debug PWA
   - Check manifest, SW, cache, storage

5. **Test on real devices**
   - Emulator ≠ real device
   - Test di Android & iOS
   - Test di slow network

---

## 📞 NEED HELP?

**Resources**:
- [MDN PWA Guide](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [web.dev PWA](https://web.dev/progressive-web-apps/)
- [PWA Builder](https://www.pwabuilder.com/)

**Debug Tools**:
- Chrome DevTools → Application
- Lighthouse audit
- [PWA Testing Tool](https://www.pwabuilder.com/test)

**Common Commands**:
```javascript
// Check SW registration
navigator.serviceWorker.getRegistration()

// Check if PWA
window.matchMedia('(display-mode: standalone)').matches

// Clear all caches
caches.keys().then(k => k.forEach(n => caches.delete(n)))

// Unregister SW
navigator.serviceWorker.getRegistrations().then(r => r.forEach(reg => reg.unregister()))
```

---

**Created**: 11 Mei 2026  
**Version**: 1.0  
**Estimated Time**: 2-4 hours for quick wins  
**Status**: Ready to Implement 🚀
