# ✅ PWA Implementation Checklist - Arradea Marketplace

> Quick reference checklist untuk implementasi PWA step-by-step

---

## 🔴 FASE 1: CRITICAL (MUST HAVE)

### Week 1: Foundation & Assets

#### Day 1-2: Icon & Asset Validation
- [ ] Cek semua icon files di `/public/images/icons/`
  - [ ] icon-72x72.png
  - [ ] icon-96x96.png
  - [ ] icon-128x128.png
  - [ ] icon-144x144.png
  - [ ] icon-152x152.png
  - [ ] icon-192x192.png
  - [ ] icon-384x384.png
  - [ ] icon-512x512.png
  - [ ] shortcut-shop.png (96x96)
  - [ ] shortcut-cart.png (96x96)
  - [ ] shortcut-orders.png (96x96)
  - [ ] badge-72x72.png

- [ ] Generate missing icons
  ```bash
  # Gunakan tool online atau ImageMagick
  # https://realfavicongenerator.net/
  # https://www.pwabuilder.com/imageGenerator
  ```

- [ ] Validasi manifest.json
  - [ ] Buka Chrome DevTools → Application → Manifest
  - [ ] Pastikan tidak ada error
  - [ ] Test semua icon sizes muncul

- [ ] Update favicon.ico
  - [ ] Generate dari icon-192x192.png
  - [ ] Place di `/public/favicon.ico`

#### Day 3-4: Service Worker Enhancement

- [ ] Backup `public/sw.js` yang lama
  ```bash
  cp public/sw.js public/sw.js.backup
  ```

- [ ] Create `public/sw-config.js`
  ```javascript
  const SW_VERSION = '1.0.0';
  const CACHE_NAMES = {
    static: `arradea-static-v${SW_VERSION}`,
    dynamic: `arradea-dynamic-v${SW_VERSION}`,
    images: `arradea-images-v${SW_VERSION}`,
  };
  ```

- [ ] Update `public/sw.js` dengan:
  - [ ] Proper cache versioning
  - [ ] Cache size limits
  - [ ] Cache expiration
  - [ ] Better error handling
  - [ ] Update notification

- [ ] Test service worker
  - [ ] Chrome DevTools → Application → Service Workers
  - [ ] Click "Update" dan pastikan tidak error
  - [ ] Check cache storage

#### Day 5: Install Prompt

- [ ] Create `resources/js/pwa-install.js`
  ```javascript
  let deferredPrompt;
  // ... implementation
  ```

- [ ] Update `resources/js/app.js`
  ```javascript
  import './pwa-install';
  ```

- [ ] Update `resources/views/components/pwa-install-button.blade.php`
  - [ ] Tambahkan ID yang benar
  - [ ] Styling responsive

- [ ] Test install prompt
  - [ ] Chrome → Settings → Install Arradea
  - [ ] Atau klik tombol install
  - [ ] Verify app terbuka standalone

### Week 2: Offline & Meta Tags

#### Day 6-7: Offline Page

- [ ] Create `resources/views/offline.blade.php`
  - [ ] Design yang match dengan brand
  - [ ] Inline critical CSS
  - [ ] Retry button
  - [ ] Cached pages list (optional)

- [ ] Build offline.html
  ```bash
  php artisan view:cache
  # Or manually copy compiled HTML to public/offline.html
  ```

- [ ] Update service worker untuk serve offline page

- [ ] Test offline mode
  - [ ] Chrome DevTools → Network → Offline
  - [ ] Navigate ke halaman baru
  - [ ] Verify offline page muncul

#### Day 8-9: Meta Tags & Headers

- [ ] Update `resources/views/layouts/app.blade.php`
  ```blade
  {{-- PWA Meta Tags --}}
  <meta name="theme-color" content="#72bf77">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="manifest" href="/manifest.json">
  ```

- [ ] Verify semua layouts punya PWA meta tags
  - [ ] dashboard.blade.php ✅ (sudah ada)
  - [ ] app.blade.php (perlu ditambah)

- [ ] Create middleware `app/Http/Middleware/PwaHeaders.php`
  ```php
  public function handle($request, Closure $next)
  {
    $response = $next($request);
    // Add PWA headers
    return $response;
  }
  ```

- [ ] Register middleware di `bootstrap/app.php`

- [ ] Update `.htaccess` atau `nginx.conf`
  ```apache
  # Cache manifest
  <FilesMatch "manifest\.json$">
    Header set Cache-Control "max-age=604800, public"
  </FilesMatch>
  ```

#### Day 10: Testing & Validation

- [ ] Run Lighthouse audit
  - [ ] Chrome DevTools → Lighthouse
  - [ ] Select "Progressive Web App"
  - [ ] Target score: > 90

- [ ] Fix Lighthouse issues
  - [ ] Manifest issues
  - [ ] Service worker issues
  - [ ] HTTPS issues
  - [ ] Icon issues

- [ ] Test di multiple devices
  - [ ] Chrome Desktop
  - [ ] Chrome Android
  - [ ] Safari iOS (add to home screen)
  - [ ] Edge Desktop

- [ ] Document issues & solutions

---

## 🟡 FASE 2: ENHANCED (SHOULD HAVE)

### Week 3: Background Sync

#### Day 11-13: IndexedDB Setup

- [ ] Install idb library
  ```bash
  npm install idb
  ```

- [ ] Create `resources/js/offline-storage.js`
  ```javascript
  import { openDB } from 'idb';
  
  export async function initDB() {
    return openDB('arradea-offline', 1, {
      upgrade(db) {
        db.createObjectStore('pending-orders');
        db.createObjectStore('cached-products');
      }
    });
  }
  ```

- [ ] Create `resources/js/order-sync.js`
  - [ ] Save order to IndexedDB when offline
  - [ ] Sync when online
  - [ ] Show sync status to user

- [ ] Update service worker untuk background sync
  ```javascript
  self.addEventListener('sync', async (event) => {
    if (event.tag === 'sync-orders') {
      event.waitUntil(syncPendingOrders());
    }
  });
  ```

#### Day 14-15: Testing Background Sync

- [ ] Test offline order creation
  - [ ] Go offline
  - [ ] Create order
  - [ ] Verify saved to IndexedDB
  - [ ] Go online
  - [ ] Verify auto-sync

- [ ] Handle sync failures
  - [ ] Show error message
  - [ ] Retry mechanism
  - [ ] Manual sync button

### Week 4: Push Notifications

#### Day 16-17: Backend Setup

- [ ] Install Laravel WebPush
  ```bash
  composer require laravel-notification-channels/webpush
  php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider"
  php artisan migrate
  ```

- [ ] Generate VAPID keys
  ```bash
  php artisan webpush:vapid
  ```

- [ ] Update `.env`
  ```
  VAPID_PUBLIC_KEY=...
  VAPID_PRIVATE_KEY=...
  ```

- [ ] Create notification classes
  - [ ] OrderStatusNotification
  - [ ] NewMessageNotification
  - [ ] NewOrderNotification (for sellers)

#### Day 18-19: Frontend Setup

- [ ] Create `resources/js/push-notifications.js`
  ```javascript
  async function subscribeToPush() {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      // Subscribe logic
    }
  }
  ```

- [ ] Add push subscription UI
  - [ ] Settings page
  - [ ] Enable/disable toggle
  - [ ] Notification preferences

- [ ] Update service worker push handler
  ```javascript
  self.addEventListener('push', (event) => {
    const data = event.data.json();
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: '/images/icons/icon-192x192.png',
    });
  });
  ```

#### Day 20: Testing Push Notifications

- [ ] Test notification permission request
- [ ] Test sending notifications
  - [ ] Order status update
  - [ ] New message
  - [ ] New order (seller)
- [ ] Test notification click action
- [ ] Test on multiple devices

---

## 🟢 FASE 3: OPTIMIZATION (NICE TO HAVE)

### Week 5: Performance

#### Day 21-23: Image Optimization

- [ ] Create image optimization component
  ```blade
  {{-- resources/views/components/image-optimized.blade.php --}}
  <img loading="lazy" ... />
  ```

- [ ] Implement lazy loading
  - [ ] Product images
  - [ ] User avatars
  - [ ] Category images

- [ ] Add WebP support
  ```blade
  <picture>
    <source srcset="image.webp" type="image/webp">
    <img src="image.jpg" alt="...">
  </picture>
  ```

- [ ] Add blur placeholder
  - [ ] Generate blur data URLs
  - [ ] Show while loading

#### Day 24-25: Code Splitting

- [ ] Update `vite.config.js`
  ```javascript
  export default {
    build: {
      rollupOptions: {
        output: {
          manualChunks: {
            'vendor': ['alpinejs', 'axios'],
            'charts': ['chart.js'],
          }
        }
      }
    }
  }
  ```

- [ ] Implement dynamic imports
  ```javascript
  // Load chart.js only on analytics page
  if (document.getElementById('analytics-chart')) {
    import('chart.js').then(Chart => {
      // Initialize chart
    });
  }
  ```

- [ ] Test bundle sizes
  ```bash
  npm run build
  # Check dist/assets/ sizes
  ```

### Week 6: Monitoring & Documentation

#### Day 26-27: Performance Monitoring

- [ ] Install web-vitals
  ```bash
  npm install web-vitals
  ```

- [ ] Create `resources/js/performance.js`
  ```javascript
  import { getCLS, getFID, getLCP } from 'web-vitals';
  // Track metrics
  ```

- [ ] Create API endpoint untuk metrics
  ```php
  Route::post('/api/analytics/web-vitals', [AnalyticsController::class, 'storeWebVitals']);
  ```

- [ ] Setup monitoring dashboard (optional)

#### Day 28-30: Documentation & Training

- [ ] Update README.md
  - [ ] PWA features
  - [ ] Installation guide
  - [ ] Development guide

- [ ] Create user guide
  - [ ] How to install app
  - [ ] How to enable notifications
  - [ ] Offline features

- [ ] Create developer guide
  - [ ] Service worker architecture
  - [ ] Cache strategy
  - [ ] Debugging tips

- [ ] Team training session
  - [ ] PWA concepts
  - [ ] Testing procedures
  - [ ] Troubleshooting

---

## 🧪 TESTING MATRIX

### Functional Tests

| Feature | Chrome Desktop | Chrome Android | Safari iOS | Edge | Firefox |
|---------|---------------|----------------|------------|------|---------|
| Install App | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| Offline Mode | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| Background Sync | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| Push Notifications | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| Shortcuts | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| Update Prompt | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |

### Performance Tests

| Metric | Target | Current | Status |
|--------|--------|---------|--------|
| Lighthouse PWA Score | > 95 | ⬜ | ⬜ |
| Performance Score | > 90 | ⬜ | ⬜ |
| First Contentful Paint | < 1.8s | ⬜ | ⬜ |
| Largest Contentful Paint | < 2.5s | ⬜ | ⬜ |
| Time to Interactive | < 3.8s | ⬜ | ⬜ |
| Cumulative Layout Shift | < 0.1 | ⬜ | ⬜ |

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Deployment

- [ ] All tests passing
- [ ] Lighthouse score > 90
- [ ] No console errors
- [ ] Service worker tested
- [ ] Offline mode tested
- [ ] Push notifications tested
- [ ] Cross-browser tested
- [ ] Mobile tested

### Deployment Steps

1. [ ] Backup production database
2. [ ] Backup current codebase
3. [ ] Deploy to staging
4. [ ] Test on staging
5. [ ] Deploy to production
6. [ ] Verify service worker registered
7. [ ] Test install prompt
8. [ ] Monitor error logs
9. [ ] Monitor performance metrics

### Post-Deployment

- [ ] Announce PWA features to users
- [ ] Monitor install rate
- [ ] Monitor error rate
- [ ] Collect user feedback
- [ ] Plan improvements

---

## 📊 SUCCESS CRITERIA

### Technical
- ✅ Lighthouse PWA Score > 95
- ✅ All PWA criteria met
- ✅ No critical errors
- ✅ Works offline
- ✅ Installable on all platforms

### User Experience
- ✅ Install rate > 10%
- ✅ Retention rate > 60%
- ✅ Positive user feedback
- ✅ Reduced bounce rate
- ✅ Increased engagement

### Business
- ✅ Mobile conversion +20%
- ✅ Page load time -40%
- ✅ Session duration +30%
- ✅ Return user rate +25%

---

## 🆘 TROUBLESHOOTING QUICK REFERENCE

### Service Worker Not Updating
```javascript
// Force update
navigator.serviceWorker.getRegistrations().then(registrations => {
  registrations.forEach(reg => reg.update());
});
```

### Clear All Caches
```javascript
caches.keys().then(names => {
  names.forEach(name => caches.delete(name));
});
```

### Unregister Service Worker
```javascript
navigator.serviceWorker.getRegistrations().then(registrations => {
  registrations.forEach(reg => reg.unregister());
});
```

### Check Install Criteria
```
Chrome DevTools → Application → Manifest
- Check all fields valid
- Check icons present
- Check service worker registered
```

---

**Last Updated**: 11 Mei 2026  
**Version**: 1.0  
**Status**: Ready to Use ✅
