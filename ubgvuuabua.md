# 🚀 Laravel UI Modern 2026 — Master Upgrade Plan (COMPLETE EDITION)
> **Primary Color:** `#72bf77` (Sage Green)  
> **Design System:** SaaS Premium — Clean, Glass, Minimal  
> **Sidebar:** Posisi sama seperti sebelumnya (fixed left), upgrade tampilannya  
> **Target Feel:** Stripe × Linear × Vercel — tapi dengan green identity yang kuat  
> **Laravel Version:** Laravel 11/12/13 (sesuaikan dengan project aktual)

---

## 🔍 PRE-FLIGHT — WAJIB DILAKUKAN SEBELUM CODING APAPUN

> ⚠️ **INI ADALAH TAHAP PALING PENTING. Jangan skip. Jangan langsung coding.**  
> Setiap prompt di bawah ini HANYA boleh dikerjakan SETELAH semua langkah pre-flight ini selesai.

---

### 🔍 PRE-FLIGHT 0 — Analisis & Evaluasi Project Laravel

```
Kamu adalah senior Laravel engineer + senior frontend architect.

SEBELUM menulis satu baris kode pun, lakukan analisis menyeluruh terhadap project Laravel ini.

LANGKAH WAJIB:

STEP 1 — SCAN STRUKTUR PROJECT:
Analisis struktur folder berikut dan laporkan temuanmu:
- app/Models/ → list semua model + relasi penting
- app/Http/Controllers/ → list semua controller per role (admin, buyer, seller)
- database/migrations/ → list semua tabel + kolom penting
- routes/web.php (dan routes/auth.php jika ada) → list semua named routes
- resources/views/ → list semua file Blade yang ada
- resources/css/ dan resources/js/ → lihat setup Vite/Mix
- config/app.php → nama app, locale, dll
- package.json → cek apakah Tailwind, Alpine.js, dsb sudah terpasang
- composer.json → versi Laravel, package tambahan

STEP 2 — BUAT MAPPING ROUTES:
Buat tabel lengkap semua route dengan format:
| Method | URI | Route Name | Controller@Method | Middleware | View Blade |
Tandai mana route yang BELUM ada view-nya (perlu dibuat).
Tandai mana route yang SUDAH ada view-nya (perlu di-redesign).

STEP 3 — BUAT MAPPING DATABASE:
Untuk setiap migration file, catat:
- Nama tabel
- Semua kolom + tipe data
- Foreign key / relasi
- Kolom enum (status, role, dll) — catat semua nilai yang valid

STEP 4 — IDENTIFIKASI POTENSI MASALAH:
- Route yang ada di web.php tapi tidak ada view-nya
- View yang ada tapi tidak terhubung ke route manapun
- Kolom yang dibutuhkan form tapi tidak ada di migration
- Middleware (auth, role, dll) yang perlu dipertimbangkan saat membuat link/redirect
- Asset pipeline: apakah pakai Vite atau Mix? versi Tailwind berapa?

STEP 5 — BUAT RENCANA KERJA:
Berdasarkan temuan di atas, buat urutan pengerjaan yang optimal.
Tandai jika ada dependencies antar file (misal: layout harus selesai sebelum halaman lain).

OUTPUT FORMAT:
Buat laporan terstruktur dengan heading yang jelas.
Di akhir laporan, sertakan ringkasan:
- Total file Blade yang perlu dibuat/redesign
- Total route yang perlu diperhatikan  
- Potensi issue yang ditemukan
- Rekomendasi urutan pengerjaan

PENTING:
- Jangan ubah APAPUN — ini hanya analisis baca saja
- Jangan generate kode dulu
- Laporan ini akan menjadi referensi untuk semua prompt selanjutnya
```

---

### 🔍 PRE-FLIGHT CHECK — Setelah Setiap Phase Selesai

```
Sebelum melanjutkan ke phase berikutnya, lakukan review menyeluruh:

CHECKLIST POST-PHASE:

□ Semua file Blade yang dihasilkan sudah dicek syntax-nya:
  - @extends merujuk ke layout yang benar
  - @section / @endsection pair match
  - @csrf ada di semua form
  - @yield / @stack didefinisikan di layout

□ Semua route() helper sudah dicek terhadap mapping routes di Pre-Flight 0:
  - Tidak ada route name yang salah tulis
  - Tidak ada route yang di-hardcode URL-nya (wajib pakai route())
  - Link navbar/sidebar sesuai dengan named routes yang ada

□ Semua form field sudah dicek terhadap migration:
  - Nama field input sesuai dengan nama kolom di database
  - Enum value (status, role, dll) sesuai dengan yang ada di migration
  - old() digunakan untuk repopulate form setelah validasi gagal

□ Logic kondisional sudah benar:
  - auth()->user()->role atau middleware check sesuai dengan struktur di database
  - Guard / middleware names sesuai dengan yang ada di project

□ Assets dan dependencies:
  - @vite() directive benar
  - CDN link tidak ada yang broken
  - Alpine.js x-data tidak conflict

□ Responsive test mental:
  - Sudah ada breakpoint untuk mobile (375px), tablet (768px), desktop (1280px)
  - Sidebar collapse behavior benar di mobile

□ Error prevention:
  - Tidak ada hardcoded data (contoh: nama user, angka dummy tanpa @php)
  - Semua variable Blade dibungkus {{ $var ?? 'default' }} atau @isset

Jika ada yang tidak lolos checklist: PERBAIKI DULU sebelum lanjut ke phase berikutnya.
```

---

## 🎨 DESIGN SYSTEM — Wajib Dipahami AI Sebelum Coding

### Color Palette
```
Primary:        #72bf77   (Sage Green — brand utama)
Primary Dark:   #5aaa60   (hover states)
Primary Light:  #a8d9ab   (tint / highlight)
Primary Muted:  #e8f5e9   (soft background tint)

Neutral 950:    #0a0f0b   (almost black — heading gelap)
Neutral 900:    #111917   (dark surface)
Neutral 800:    #1e2a20   (sidebar dark)
Neutral 700:    #2d3d2f   (border dark)
Neutral 500:    #6b7c6d   (muted text)
Neutral 300:    #c8d4c9   (border light)
Neutral 100:    #f0f5f0   (background tint)
Neutral 50:     #f8faf8   (page background)
White:          #ffffff

Glass Surface:  rgba(255,255,255,0.72)  backdrop-blur: 12px
Glass Border:   rgba(114,191,119,0.18)
Shadow Soft:    0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.05)
Shadow Medium:  0 4px 24px rgba(0,0,0,0.09), 0 1px 4px rgba(0,0,0,0.04)
```

### Typography
```
Display / Heading:  'Plus Jakarta Sans', sans-serif  (weight 600–800)
Body:               'DM Sans', sans-serif             (weight 400–500)
Mono / Code:        'JetBrains Mono', monospace

Import CDN:
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
```

### Border Radius
```
xs:   4px   (badges, chips kecil)
sm:   8px   (buttons, inputs)
md:   12px  (cards kecil)
lg:   16px  (cards utama)
xl:   20px  (modal, panel besar)
2xl:  28px  (hero section)
full: 9999px (pill buttons, avatars)
```

### Spacing Rhythm
Gunakan spacing natural — bukan grid robotik. Kombinasikan:
- Padding section: 32px–48px
- Gap antar card: 16px–24px
- Padding card dalam: 20px–28px
- Micro spacing (icon–text): 8px–12px

### Micro-interactions Standard
```css
/* Semua elemen interaktif pakai ini */
transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);

/* Hover lift untuk card */
transform: translateY(-2px);
box-shadow: 0 8px 32px rgba(0,0,0,0.12);

/* Button active press */
transform: scale(0.97);
```

---

## 📦 FASE 1 — FOUNDATION & LAYOUT UTAMA

> ⚠️ **WAJIB SELESAIKAN PRE-FLIGHT 0 DULU SEBELUM FASE INI**  
> Setelah fase ini selesai, jalankan POST-PHASE CHECK sebelum lanjut ke Fase 2.

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 1:

```
SEBELUM MENULIS KODE:
1. Rujuk ke hasil Pre-Flight 0 — gunakan route names yang TEPAT dari mapping routes project ini
2. Rujuk ke migration database — jangan buat kolom atau field yang tidak ada di schema
3. Cek package.json — konfirmasi versi Tailwind dan apakah Alpine.js sudah ada
4. Jangan mengubah file backend (Controller, Model, Migration) apapun
```

---

### PROMPT 1.1 — Design System CSS Global

```
Kamu adalah senior frontend engineer SaaS premium level Stripe/Linear/Vercel.

KONTEKS PROJECT:
[PASTE HASIL ANALISIS PRE-FLIGHT 0 DI SINI — terutama bagian tech stack dan asset pipeline]
[CONTOH: "Project menggunakan Tailwind CSS 3.x, Alpine.js 3.x via CDN, Vite"]

Buat file CSS global untuk Laravel project dengan design system berikut.

SEBELUM MENULIS:
- Cek apakah project menggunakan Tailwind atau CSS murni (dari Pre-Flight 0)
- Jika Tailwind: CSS ini sebagai tambahan, JANGAN override Tailwind default
- Jika CSS murni: CSS ini adalah foundation utama

WAJIB INCLUDE:
1. CSS Custom Properties (variables) untuk:
   - Color palette: primary #72bf77, primary-dark #5aaa60, primary-light #a8d9ab, primary-muted #e8f5e9
   - Neutrals: 950 (#0a0f0b), 900 (#111917), 800 (#1e2a20), 700 (#2d3d2f), 500 (#6b7c6d), 300 (#c8d4c9), 100 (#f0f5f0), 50 (#f8faf8)
   - Glass: --glass-bg: rgba(255,255,255,0.72), --glass-border: rgba(114,191,119,0.18)
   - Shadow: --shadow-sm, --shadow-md, --shadow-lg
   - Radius: --radius-xs (4px) sampai --radius-2xl (28px)
   - Font: --font-display: 'Plus Jakarta Sans', --font-body: 'DM Sans', --font-mono: 'JetBrains Mono'

2. Base reset & body styling:
   - background: var(--neutral-50) #f8faf8
   - font-family: var(--font-body)
   - color: var(--neutral-950)
   - smooth scrolling, box-sizing border-box

3. Komponen utility classes:
   .glass-card { background: var(--glass-bg); backdrop-filter: blur(12px); border: 1px solid var(--glass-border); }
   .btn-primary { background: #72bf77; color: white; border-radius: 8px; padding: 10px 20px; font-weight: 600; transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }
   .btn-primary:hover { background: #5aaa60; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(114,191,119,0.35); }
   .btn-ghost, .badge-green, .badge-gray, .badge-red, .badge-yellow
   .card { border-radius: 16px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.05); border: 1px solid var(--neutral-100); }
   .input { border-radius: 8px; border: 1.5px solid var(--neutral-300); padding: 10px 14px; transition: border-color 0.18s; }
   .input:focus { border-color: #72bf77; box-shadow: 0 0 0 3px rgba(114,191,119,0.15); outline: none; }

4. Animasi global:
   @keyframes fadeInUp { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform: translateY(0); } }
   @keyframes shimmer (untuk skeleton loading)
   @keyframes slideInLeft (untuk sidebar mobile)
   .animate-fadein { animation: fadeInUp 0.4s ease forwards; }
   .stagger-1 { animation-delay: 0.05s; } sampai .stagger-5

5. Scrollbar styling:
   - Thin, green-tinted scrollbar
   - width 6px, track transparent, thumb #c8d4c9 hover #72bf77

6. Tailwind-compatible custom classes (jika project pakai Tailwind):
   - Extend dengan CSS variables di atas
   - Jangan override Tailwind default, tambahkan saja

OUTPUT: File resources/css/app-modern.css yang siap include di layout.
Tidak perlu penjelasan. Langsung code.
```

---

### PROMPT 1.2 — Layout Utama: `resources/views/layouts/app.blade.php`

```
Kamu adalah senior frontend engineer SaaS premium.

KONTEKS PROJECT:
[PASTE HASIL ANALISIS PRE-FLIGHT 0 DI SINI]
[TERUTAMA: daftar routes public/guest, nama app dari config/app.php]

INSTRUKSI KRITIS — BACA SEBELUM CODING:
1. Gunakan HANYA route names yang ada di hasil Pre-Flight 0
2. Untuk navbar links: sesuaikan dengan routes yang benar-benar ada
3. Jangan hardcode URL — selalu pakai route() atau url() helper
4. Jika route tertentu tidak ada di project ini, jangan buat linknya

Redesign FULL file resources/views/layouts/app.blade.php untuk Laravel.

CONTEXT:
- Ini adalah layout utama untuk halaman PUBLIC (welcome, auth, dll)
- Bukan layout dashboard — tidak ada sidebar di sini
- Harus menyertakan navbar top yang modern

DESIGN REQUIREMENTS:

1. <head> section:
   - Include Google Fonts: Plus Jakarta Sans, DM Sans, JetBrains Mono
   - Include Alpine.js dari CDN (x-data, x-show, @click support)
   - Include @vite(['resources/css/app.css', 'resources/js/app.js']) atau @stack('styles')
   - Meta tags lengkap (viewport, charset, CSRF)

2. Navbar (untuk halaman public/guest):
   - Fixed top, glassmorphism effect: backdrop-filter: blur(14px); background: rgba(255,255,255,0.82); border-bottom: 1px solid rgba(114,191,119,0.15);
   - Height: 64px
   - Logo kiri: teks brand dengan dot berwarna #72bf77
   - Nav links tengah/kanan: clean, font DM Sans weight 500
   - CTA button: "Masuk" ghost + "Daftar" filled #72bf77 (gunakan route('login') dan route('register') — verifikasi nama ini dari Pre-Flight 0)
   - Mobile: hamburger menu dengan Alpine.js toggle
   - Smooth scroll behavior jika ada anchor links

3. Main content area:
   - min-height: 100vh
   - Background: #f8faf8
   - @yield('content')

4. Footer minimal:
   - Border top #f0f5f0
   - Copyright text, links kecil
   - Padding 32px 0

5. JavaScript global:
   - Toast notification system (Blade flash message)
   - Page transition subtle (opacity fade)
   - Detect scroll untuk navbar shadow effect

6. @stack('scripts') di akhir body

CONSTRAINTS:
- Jangan ubah backend Laravel
- Blade syntax tetap (@yield, @section, @push, dll)
- Semua class menggunakan Tailwind atau custom CSS

OUTPUT: Full Blade file siap pakai. Tidak perlu penjelasan.

=== SETELAH SELESAI ===
Lakukan mini-review: sebutkan semua route() yang kamu gunakan dan konfirmasi bahwa semua ada di hasil Pre-Flight 0.
```

---

### PROMPT 1.3 — Layout Dashboard: `resources/views/layouts/dashboard.blade.php`

```
Kamu adalah senior frontend engineer SaaS premium level Linear/Vercel.

KONTEKS PROJECT:
[PASTE HASIL ANALISIS PRE-FLIGHT 0 DI SINI]
[TERUTAMA: struktur middleware auth, named routes dashboard, cara check role user]

INSTRUKSI KRITIS — BACA SEBELUM CODING:
1. Cek dari Pre-Flight 0: bagaimana cara check role user? (contoh: auth()->user()->role === 'admin' ATAU $user->hasRole('admin') tergantung project)
2. Cek route name untuk logout — apakah 'logout' atau nama lain?
3. Semua link di topbar/navbar harus pakai route() yang ada
4. Jangan buat kondisi role yang tidak sesuai dengan enum di database

Redesign FULL file resources/views/layouts/dashboard.blade.php untuk Laravel.

CONTEXT:
- Layout untuk semua halaman dashboard (admin, seller, buyer)
- Sidebar di KIRI (posisi sama seperti sebelumnya — JANGAN PINDAH)
- Main content area di KANAN
- Navbar top di dalam content area (bukan menutupi sidebar)

SIDEBAR SPECS:
- Width: 260px (desktop), collapsed: 0 (mobile dengan overlay)
- Background: #0a0f0b (near black) ATAU white dengan border kanan — pilih salah satu yang lebih premium
- Position: fixed left, full height
- Scrollable jika konten panjang (scrollbar thin)
- Padding: 24px 16px

TOPBAR SPECS (di dalam main content, bukan full width):
- Height: 60px
- Glassmorphism: backdrop-filter blur(8px), background rgba(248,250,248,0.9)
- Border bottom: 1px solid #f0f5f0
- Sticky top: 0
- Konten: breadcrumb/page title (kiri), search + notif + avatar (kanan)
- User avatar: circle dengan initial nama atau foto
- Notification bell: dengan dot badge merah jika ada

MAIN CONTENT:
- margin-left: 260px (desktop), margin-left: 0 (mobile)
- padding: 32px
- background: #f8faf8

MOBILE BEHAVIOR:
- Sidebar tersembunyi (transform: translateX(-260px))
- Hamburger di topbar untuk toggle sidebar
- Overlay gelap saat sidebar terbuka (klik overlay → tutup sidebar)
- Alpine.js untuk toggle state

JAVASCRIPT WAJIB ADA:
- Sidebar toggle (mobile)
- Active menu detection (berdasarkan URL saat ini)
- Flash message / toast (dari session Laravel: success, error, warning, info)
   Format toast: pojok kanan bawah, slide in, auto dismiss 4 detik, bisa di-dismiss manual
- Keyboard shortcut: Ctrl+B untuk toggle sidebar
- Smooth page transitions

SIDEBAR COMPONENT LOADING:
- Load sidebar berdasarkan role: @if(auth()->user()->role === '[SESUAIKAN DENGAN ENUM DI MIGRATION]')
   @include('components.sidebar.admin')
   @elseif ... dst
- Sesuaikan kondisi dengan struktur role yang ada di database (dari Pre-Flight 0)

BLADE SECTIONS:
- @yield('content') untuk konten utama
- @yield('page-title') untuk judul di topbar
- @stack('styles') di head
- @stack('scripts') di akhir body

OUTPUT: Full Blade file. Tidak perlu penjelasan. Code siap deploy.

=== SETELAH SELESAI ===
Lakukan mini-review: sebutkan semua kondisi role dan route() yang kamu gunakan, konfirmasi sesuai Pre-Flight 0.
```

---

## 📦 FASE 2 — KOMPONEN SIDEBAR

> ⚠️ **Pastikan POST-PHASE CHECK Fase 1 sudah selesai sebelum mulai Fase 2.**  
> Sidebar harus konsisten dengan route names dan role values dari database migration.

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 2:

```
SEBELUM MENULIS KODE SIDEBAR:
1. Buka hasil Pre-Flight 0 — ambil semua route names untuk role ini
2. Buka migration tabel users — konfirmasi nilai enum untuk role
3. Setiap nav item HANYA boleh ada jika route-nya ada di web.php
4. Gunakan Request::routeIs() dengan route name yang TEPAT dari hasil analisis
5. Jangan buat link ke route yang tidak ada
```

---

### PROMPT 2.1 — Sidebar Admin: `resources/views/components/sidebar/admin.blade.php`

```
Kamu adalah senior UI engineer. Redesign sidebar admin Laravel.

KONTEKS PROJECT:
[PASTE DAFTAR ROUTE ADMIN DARI PRE-FLIGHT 0 DI SINI]
[CONTOH:
  admin.dashboard → GET /admin/dashboard
  admin.users.index → GET /admin/users
  admin.sellers.index → GET /admin/sellers
  dll...]

INSTRUKSI KRITIS:
- Gunakan HANYA route names yang ada di daftar di atas
- Jika suatu fitur tidak ada route-nya, JANGAN buat nav item-nya
- Request::routeIs() harus menggunakan nama route yang PERSIS sama

DESIGN SPECS:
- Background: dark (#111917) dengan teks putih/abu
- Logo area (top): nama app + logo, padding 24px 20px, border-bottom subtle
- User info section: avatar bulat + nama admin + role badge "Admin"
- Nav items: icon + label, padding 10px 14px, border-radius 8px
- Active state: background #72bf77 teks putih, ATAU left border 3px #72bf77 dengan background rgba(114,191,119,0.1)
- Hover state: background rgba(255,255,255,0.06)
- Section divider: label kecil uppercase tracking-wide opacity-50 (contoh: "MANAGEMENT", "SYSTEM")
- Icons: Heroicons SVG inline (bukan font icon)
- Bottom section: Profile link + Logout button

NAV ITEMS ADMIN (sesuaikan dengan route yang ada di project):
- Dashboard (grid icon) → route('admin.dashboard') [sesuaikan nama route]
- --- MANAGEMENT ---
- Sellers (store icon) → route('admin.sellers.index') [sesuaikan]
- Users (users icon) → route('admin.users.index') [sesuaikan]
- User Verification (shield-check icon) → [sesuaikan]
- Verifications (document-check icon) → [sesuaikan]
- Access Codes (key icon) → [sesuaikan]
- --- ANALYTICS ---
- Map Users (map icon) → [sesuaikan]

CATATAN: Jika route name di atas tidak persis sama dengan yang ada di project,
gunakan nama yang BENAR dari hasil Pre-Flight 0. Jangan tebak.

HOVER & TRANSITION:
- Semua item: transition: all 0.15s ease
- Icon: opacity 0.7 → 1 saat hover/active
- Active item sedikit bold

BLADE SYNTAX:
- Gunakan {{ Request::routeIs('admin.*') ? 'active-class' : '' }} untuk active state
- Link pakai route() helper
- Logout: form POST ke route('logout') [sesuaikan nama route dari Pre-Flight 0]

OUTPUT: Full Blade component. Tidak perlu penjelasan.
```

---

### PROMPT 2.2 — Sidebar Buyer: `resources/views/components/sidebar/buyer.blade.php`

```
Kamu adalah senior UI engineer. Redesign sidebar buyer Laravel.

KONTEKS PROJECT:
[PASTE DAFTAR ROUTE BUYER DARI PRE-FLIGHT 0 DI SINI]

INSTRUKSI KRITIS:
- Gunakan HANYA route names yang ada di project
- Untuk cart badge counter: sesuaikan dengan cara project menyimpan cart (session atau database — cek dari Pre-Flight 0)
- Cek apakah ada kolom untuk menghitung unread messages / wishlist di schema

DESIGN SPECS:
- Background: WHITE dengan shadow kanan halus (border-right: 1px solid #f0f5f0)
  ATAU dark #111917 seperti admin (konsistenkan dengan pilihan di layout dashboard)
- Logo area: sama dengan admin sidebar
- User info: avatar + nama buyer + email kecil
- Nav items: style konsisten dengan sidebar admin

NAV ITEMS BUYER (sesuaikan dengan routes yang ada):
- Dashboard (home icon)
- --- BELANJA ---
- Produk (shopping-bag icon)
- Keranjang (shopping-cart icon) + badge counter
- Wishlist (heart icon) + badge counter
- --- PESANAN ---
- Pesanan Saya (clipboard-list icon)
- --- AKUN ---
- Profil (user icon)
- Chat (chat-bubble icon) + badge unread

SPECIAL: Keranjang badge counter
- Sesuaikan cara ambil data dengan struktur project:
  Jika cart di session: session('cart') → hitung
  Jika cart di database: cek model Cart dari Pre-Flight 0
- Badge: angka kecil merah/green di pojok kanan icon

OUTPUT: Full Blade component. Tidak perlu penjelasan.
```

---

### PROMPT 2.3 — Sidebar Seller: `resources/views/components/sidebar/seller.blade.php`

```
Kamu adalah senior UI engineer. Redesign sidebar seller Laravel.

KONTEKS PROJECT:
[PASTE DAFTAR ROUTE SELLER DARI PRE-FLIGHT 0 DI SINI]
[PASTE KOLOM STATUS/VERIFIKASI SELLER DARI MIGRATION DI SINI]
[CONTOH: "Tabel sellers memiliki kolom 'status' dengan enum: pending, verified, rejected"]

INSTRUKSI KRITIS:
- Cek nama kolom status seller dari migration — jangan tebak (bisa 'status', 'is_verified', 'verification_status', dll)
- Sesuaikan kondisi status badge dengan nilai enum yang ada
- Gunakan route names yang ada

NAV ITEMS SELLER (sesuaikan dengan routes yang ada):
- Dashboard (chart-bar icon)
- --- TOKO ---
- Produk Saya (cube icon)
- Tambah Produk (plus-circle icon)
- Pesanan Masuk (inbox icon) + badge order baru
- --- BISNIS ---
- Analitik (chart-line icon)
- Pesan (chat icon) + badge unread
- --- PENGATURAN ---
- Pengaturan Toko (cog icon)
- Profil (user icon)

DESIGN sama dengan sidebar sebelumnya (konsisten dalam satu project).

SPECIAL: Seller status badge
- Ambil dari kolom yang BENAR di tabel sellers/users (dari Pre-Flight 0)
- Jika seller belum terverifikasi: tampilkan chip "Pending Approval" kuning
- Jika terverifikasi: chip "Verified" hijau
- Sesuaikan kondisi: auth()->user()->seller->status === '[nilai dari migration]'

OUTPUT: Full Blade component. Tidak perlu penjelasan.
```

---

## 📦 FASE 3 — HALAMAN PUBLIC & AUTH

> ⚠️ **Pastikan POST-PHASE CHECK Fase 2 sudah selesai sebelum mulai Fase 3.**

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 3:

```
SEBELUM MENULIS KODE AUTH:
1. Dari Pre-Flight 0: konfirmasi route names untuk login, register, logout, verify, dll
2. Dari migration: konfirmasi kolom yang ada di tabel users (nama field yang benar)
3. Jangan tambah field di form yang tidak ada di migration / tidak dihandle controller
4. Gunakan @error('field_name') dengan nama field yang sesuai request validation
```

---

### PROMPT 3.1 — Welcome Page: `resources/views/welcome.blade.php`

```
Kamu adalah senior frontend engineer + UI/UX designer SaaS premium.

KONTEKS PROJECT:
[PASTE NAMA APP, DESKRIPSI, DAN ROUTE PUBLIC DARI PRE-FLIGHT 0]

INSTRUKSI KRITIS:
- CTA buttons harus mengarah ke route yang benar (dari Pre-Flight 0)
- Jangan tampilkan fitur atau stat yang tidak ada di project ini
- Sesuaikan copy text dengan nama app yang sebenarnya

Redesign FULL resources/views/welcome.blade.php — halaman landing page untuk marketplace Laravel.

SECTIONS YANG WAJIB ADA:

1. HERO SECTION:
   - Headline besar (Plus Jakarta Sans, 56–72px, font-weight 800)
   - Subheadline deskripsi produk
   - 2 CTA: "Mulai Belanja" (#72bf77 filled) + "Daftar Sebagai Seller" (ghost)
     [gunakan route names yang benar dari Pre-Flight 0]
   - Background: gradient halus dari #f8faf8 ke #e8f5e9, atau grain texture subtle
   - Floating shapes / blob dengan warna #72bf77 opacity rendah sebagai dekorasi
   - Gambar hero atau ilustrasi placeholder (gunakan unsplash placeholder atau CSS art)

2. STATS SECTION:
   - 3-4 angka besar (contoh: "1.200+ Produk", "500+ Seller", "10.000+ Pembeli")
   - Card kecil dengan animasi count-up saat masuk viewport

3. FEATURES SECTION:
   - 3 kolom grid
   - Setiap feature: icon besar (#72bf77), judul, deskripsi
   - Cards dengan glass effect

4. CATEGORIES SHOWCASE:
   - Grid kategori dengan warna berbeda tiap card
   - Hover: scale subtle

5. CTA SECTION BAWAH:
   - Background: #72bf77 gradient
   - Teks putih
   - 1 tombol besar

6. FOOTER:
   - Links, copyright

JAVASCRIPT:
- Intersection Observer untuk animasi saat scroll (fadeInUp)
- Count-up animation untuk stats
- Parallax halus pada hero

OUTPUT: Full Blade file extends('layouts.app'). Tidak perlu penjelasan.
```

---

### PROMPT 3.2 — Login Page: `resources/views/auth/login.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman login Laravel.

KONTEKS PROJECT:
[PASTE INFO BERIKUT DARI PRE-FLIGHT 0:
 - Route name untuk POST login (contoh: 'login')
 - Field yang digunakan login: email atau username? (dari migration)
 - Apakah ada 'remember me' feature?
 - Route name untuk forgot password jika ada]

INSTRUKSI KRITIS:
- action="{{ route('[NAMA ROUTE LOGIN YANG BENAR]') }}"
- Nama input field (email/username/password) harus sesuai dengan yang diexpect controller
- @error() harus menggunakan nama field yang benar

LAYOUT: Split screen
- KIRI (40%): Branding panel
   - Background: linear-gradient(135deg, #0a0f0b 0%, #1e2a20 100%)
   - Logo / Brand name besar
   - Tagline / quote
   - Decorative element: pattern atau abstract shape warna #72bf77
   - Testimonial kecil atau feature highlight di bagian bawah

- KANAN (60%): Form panel
   - Background: white
   - Padding: 48px–64px
   - Center vertical

FORM ELEMENTS:
- Judul: "Selamat Datang Kembali" (Plus Jakarta Sans, bold)
- Subtitle: "Masuk ke akun Anda"
- Input email: label floating atau label atas, dengan icon envelope
- Input password: dengan toggle show/hide (mata icon)
- Remember me: custom checkbox style (green accent)
- Tombol Login: full width, #72bf77, height 48px, font-weight 600
- Link "Lupa Password?" kanan align [hanya jika route-nya ada]
- Divider "atau"
- Link ke register [pakai route yang benar]
- Flash error message: styled red alert dengan icon X

JAVASCRIPT:
- Show/hide password toggle
- Form loading state (tombol jadi spinner saat submit)
- Shake animation jika ada error validasi
- Input focus effects

BLADE:
- @csrf
- action="{{ route('[ROUTE LOGIN]') }}" method="POST"
- old() untuk repopulate, @error() untuk validasi

OUTPUT: Full Blade file. Layout 2 kolom, tidak ada sidebar. Tidak perlu penjelasan.
```

---

### PROMPT 3.3 — Register Page: `resources/views/auth/register.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman register Laravel.

KONTEKS PROJECT:
[PASTE INFO BERIKUT DARI PRE-FLIGHT 0:
 - Route name untuk POST register
 - Kolom yang ada di tabel users (dari migration) — hanya tampilkan field yang ada
 - Apakah ada kolom phone/no_hp? apakah ada role selection saat register?]

INSTRUKSI KRITIS:
- Form field HANYA untuk kolom yang ada di tabel users migration
- Jangan tambah field yang tidak ada di migration atau tidak divalidasi controller
- Nama input harus sesuai dengan yang diexpect oleh RegisterController / StoreRequest

LAYOUT: Sama dengan login — split screen ATAU centered card (pilih yang lebih premium)

FORM ELEMENTS (sesuaikan dengan kolom migration):
- Judul: "Buat Akun Baru"
- Input sesuai migration (contoh standar: Nama Lengkap, Email, Password, Konfirmasi Password)
- Tambahkan hanya jika ada di migration: No. HP, pilihan role, dll
- Password strength indicator (weak/medium/strong dengan bar warna)
- Checkbox persetujuan Terms of Service
- Tombol "Daftar Sekarang": full width, #72bf77

JAVASCRIPT:
- Password strength meter real-time
- Konfirmasi password match indicator (checkmark hijau / X merah)
- Phone number formatting (jika ada field HP)
- Form validation feedback sebelum submit

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 3.4 — Verify Phone: `resources/views/auth/verify-phone.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman verifikasi OTP nomor HP.

KONTEKS PROJECT:
[PASTE INFO BERIKUT DARI PRE-FLIGHT 0:
 - Apakah fitur verify phone ada di routes?
 - Route name untuk POST verify dan resend OTP
 - Nama field yang diexpect (kode, token, dll)]

INSTRUKSI KRITIS:
- Jika fitur ini tidak ada di routes project, SKIP file ini dan beritahu user
- action form harus menggunakan route name yang benar
- Nama input field OTP sesuai dengan yang diexpect controller

LAYOUT: Centered card, background #f8faf8

DESIGN:
- Icon besar: ilustrasi HP atau shield (SVG inline, warna #72bf77)
- Judul: "Verifikasi Nomor HP"
- Subtitle: "Masukkan kode OTP yang dikirim ke +62xxx"
- OTP Input: 6 kotak terpisah (digit per kotak)
   - Auto-focus ke kotak berikutnya saat diisi
   - Auto-submit saat kotak ke-6 terisi
   - Backspace → kembali ke kotak sebelumnya
   - Paste support (paste 6 digit langsung isi semua)
   - Style: kotak besar (56x56px), border 2px, focus: border #72bf77
- Timer resend OTP (countdown 60 detik)
- Link "Kirim Ulang" (disabled saat countdown, aktif setelahnya)
- Tombol Verifikasi

JAVASCRIPT:
- OTP box navigation logic
- Countdown timer
- Auto-submit

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 3.5 — Verify Admin Approval: `resources/views/auth/verify-admin-approval.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman waiting admin approval.

KONTEKS PROJECT:
[PASTE INFO BERIKUT DARI PRE-FLIGHT 0:
 - Apakah ada kolom status approval di tabel users? nama kolom dan nilai enum-nya?
 - Route untuk logout
 - Apakah ada endpoint untuk check status (polling)?]

LAYOUT: Centered, full height, background #f8faf8

DESIGN:
- Ilustrasi: Animated SVG atau CSS animation (jam pasir, loading dots, atau ilustrasi review)
- Judul: "Akun Sedang Ditinjau"
- Deskripsi: penjelasan proses, estimasi waktu
- Status steps (sesuaikan label dengan alur project ini):
  1. ✅ Pendaftaran Selesai
  2. 🔄 Menunggu Persetujuan Admin (active, animated pulse)
  3. ⏳ Akun Aktif
- Info kontak support jika butuh bantuan
- Tombol Logout → route('logout') [sesuaikan nama route]
- Auto-refresh setiap 30 detik (check status, tanpa reload penuh)

JAVASCRIPT:
- Animated pulse pada step yang aktif
- Polling status dengan fetch ke endpoint yang ada (jika ada dari Pre-Flight 0)

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

## 📦 FASE 4 — HALAMAN BUYER

> ⚠️ **Pastikan POST-PHASE CHECK Fase 3 sudah selesai sebelum mulai Fase 4.**

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 4:

```
SEBELUM MENULIS KODE BUYER:
1. Dari Pre-Flight 0: konfirmasi semua route names buyer
2. Dari migration: konfirmasi struktur tabel products, orders, cart, wishlist
3. Setiap kolom yang ditampilkan (harga, nama, status, dll) harus sesuai nama kolom di migration
4. Status order: gunakan nilai enum yang benar dari migration (jangan pakai 'pending' jika enum-nya 'waiting')
5. Setiap form action harus menggunakan route name yang benar
```

---

### PROMPT 4.1 — Buyer Dashboard: `resources/views/buyer/dashboard.blade.php`

```
Kamu adalah senior UI engineer. Redesign dashboard buyer Laravel.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names buyer
 - Struktur tabel orders (kolom status, kolom total, dll)
 - Struktur tabel products
 - Apakah ada tabel wishlist? apakah ada cart?]

INSTRUKSI KRITIS:
- Variabel Blade ($orders, $products, dll) harus sesuai dengan yang dipass controller
- Nama kolom di template ($order->total_price atau $order->total? cek dari migration)
- Status badge: gunakan nilai enum yang tepat dari migration
- Semua route() harus ada di project

LAYOUT: Extends dashboard layout, sidebar kiri (sudah ada dari layout)

SECTIONS:

1. HEADER GREETING:
   - "Halo, {{ auth()->user()->name }}! 👋" (Plus Jakarta Sans, besar)
   - Tanggal hari ini
   - Subtitle pendek

2. STATS CARDS (1 baris, 4 kolom):
   - Total Pesanan | Pesanan Aktif | Total Wishlist | Total Belanja (Rp)
   - Setiap card: icon warna-warni, angka besar, label kecil
   - Shadow halus, border-radius 16px
   - Hover: sedikit lift
   - Data dari variabel controller (sesuaikan nama variabel dengan yang ada di controller)

3. PESANAN TERBARU (tabel atau card list):
   - 5 pesanan terakhir
   - Setiap item: nama produk, tanggal, status badge, total, tombol "Lihat Detail"
   - Status badge: sesuaikan warna dengan nilai enum dari migration
   - Empty state jika belum ada pesanan: ilustrasi + CTA belanja

4. PRODUK REKOMENDASI / TERBARU:
   - Grid 4 kolom (desktop), 2 kolom (mobile)
   - Product card: gambar, nama, harga, tombol add to cart
   - Skeleton loading state

JAVASCRIPT:
- Animasi counter angka stats saat load
- Skeleton loading untuk produk (shimmer effect)
- Add to cart dengan feedback toast

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.2 — Produk Index (Buyer): `resources/views/buyer/products/index.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman daftar produk untuk buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk produk buyer
 - Struktur tabel products (kolom: nama, harga, stok, gambar, kategori_id, dll)
 - Struktur tabel categories
 - Route untuk add to cart dan wishlist]

INSTRUKSI KRITIS:
- Nama kolom produk (nama/name, harga/price, gambar/image, dll) sesuai migration
- Route untuk cart/wishlist harus ada di project
- Filter kategori: gunakan data dari tabel categories

LAYOUT: Extends dashboard layout

SECTIONS:

1. HEADER + FILTER BAR:
   - Judul halaman + jumlah produk ditemukan
   - Search bar lebar dengan icon
   - Filter chips: Kategori (dropdown), Harga (range slider), Urutkan (select)
   - Mobile: filter dalam drawer/modal

2. PRODUCT GRID:
   - Default: 4 kolom (desktop), 2 kolom (tablet), 1 kolom (mobile)
   - Toggle view: Grid ↔ List view
   - Product card:
     - Gambar (aspect ratio 1:1 atau 4:3, object-fit cover)
     - Badge: "Baru", "Diskon", "Terlaris" (hanya jika ada kolom yang mendukung di migration)
     - Nama produk (2 baris max, text-ellipsis) — nama kolom sesuai migration
     - Nama toko / seller (kecil, abu)
     - Harga: bold, warna #72bf77 — nama kolom sesuai migration
     - Harga coret jika ada diskon (hanya jika ada kolom diskon/harga_coret di migration)
     - Rating bintang (hanya jika ada tabel reviews/ratings)
     - Tombol: "Tambah ke Keranjang" + icon hati wishlist
     - Hover card: tombol muncul dengan animasi

3. PAGINATION:
   - {{ $products->links() }} dengan styling kustom

4. EMPTY STATE:
   - Jika tidak ada produk: ilustrasi SVG + pesan + reset filter button

JAVASCRIPT:
- Filter tanpa reload (Alpine.js atau fetch)
- Grid/List view toggle dengan animasi
- Wishlist toggle dengan heart animation
- Add to cart feedback

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.3 — Produk Detail (Buyer): `resources/views/buyer/products/show.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman detail produk buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route name untuk show produk, add to cart, toggle wishlist
 - Struktur tabel products lengkap
 - Apakah ada tabel product_images atau gambar disimpan satu kolom?
 - Apakah ada tabel reviews/ulasan?
 - Struktur tabel sellers/stores]

INSTRUKSI KRITIS:
- Galeri gambar: sesuaikan dengan cara penyimpanan gambar di project (single kolom vs relasi)
- Rating: hanya tampilkan jika ada tabel reviews di migration
- Seller info: sesuaikan dengan struktur tabel sellers/stores yang ada

LAYOUT: Extends dashboard layout

SECTIONS:

1. BREADCRUMB: sesuaikan dengan route yang ada

2. PRODUCT MAIN SECTION (2 kolom):
   KIRI — Galeri Gambar:
   - Sesuaikan dengan struktur data gambar (single atau multiple dari tabel relasi)

   KANAN — Info Produk:
   - Semua kolom sesuai migration
   - Harga coret: hanya jika ada kolom di migration
   - Stok: nama kolom sesuai migration (stock/stok/qty?)
   - Tombol aksi menggunakan route yang benar

3. PRODUCT DESCRIPTION TAB:
   - Tab: Deskripsi | Spesifikasi (hanya jika ada kolomnya) | Ulasan (hanya jika ada tabel reviews)

4. RELATED PRODUCTS

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.4 — Keranjang: `resources/views/buyer/cart/index.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman keranjang belanja buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk cart (update qty, remove, checkout)
 - Struktur tabel carts (apakah pakai database atau session?)
 - Struktur tabel products untuk join
 - Apakah ada fitur kode promo? ada tabel coupons/promo_codes?]

INSTRUKSI KRITIS:
- Sesuaikan cara ambil data cart (session vs database)
- Route untuk update dan remove harus yang benar dari project
- Kode promo: hanya tampilkan jika ada di project (ada route dan tabel-nya)
- Checkout button mengarah ke route checkout yang benar

LAYOUT: Extends dashboard layout

SECTIONS (2 kolom):

KIRI (60%) — DAFTAR ITEM:
- Header "Keranjang Belanja (X item)"
- Setiap item sesuai struktur data yang ada
- Animasi remove item (slide out + fade)
- Empty cart state: ilustrasi + CTA belanja

KANAN (40%) — SUMMARY:
- Card "Ringkasan Pesanan" (sticky saat scroll)
- Kalkulasi total sesuai struktur data
- Input kode promo (hanya jika fitur ada)
- Tombol "Checkout" → route yang benar

JAVASCRIPT:
- Update quantity: AJAX ke route yang benar
- Remove item dengan animasi
- Promo code (jika fitur ada)
- Sticky summary on scroll

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.5 — Orders: `resources/views/buyer/orders/index.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman daftar pesanan buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk orders buyer
 - Struktur tabel orders + kolom status
 - Nilai-nilai enum untuk status order (PENTING: jangan tebak)]

INSTRUKSI KRITIS:
- Tab filter status HARUS menggunakan nilai enum yang benar dari migration
- Badge count per tab harus query yang benar
- Nama kolom (nomor order, tanggal, total) sesuai migration

LAYOUT: Extends dashboard layout

SECTIONS:

1. HEADER + FILTER TABS:
   - Tab filter: sesuaikan dengan nilai enum status di migration
   - Jangan tambah tab yang tidak ada nilai enum-nya

2. ORDER LIST:
   - Setiap order: card layout
   - Kolom sesuai migration
   - Multiple produk dalam 1 order: tampilkan +N lagi

3. PAGINATION: {{ $orders->links() }}

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.6 — Order Detail: `resources/views/buyer/orders/show.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman detail pesanan buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Struktur tabel orders + relasi ke order_items, products
 - Kolom status dan nilai enum-nya
 - Route untuk confirm received, cancel order, chat seller
 - Apakah ada kolom shipping/pengiriman? nama tabelnya?]

INSTRUKSI KRITIS:
- Stepper progress: sesuaikan steps dengan nilai enum status yang ada
- Tombol aksi: hanya tampilkan yang route-nya ada di project
- Info pengiriman: sesuaikan dengan kolom yang ada di migration

SECTIONS:

1. HEADER
2. ORDER TIMELINE: sesuaikan steps dengan enum di migration
3. DETAIL PRODUK: sesuaikan dengan struktur order_items
4. INFO PENGIRIMAN: sesuaikan dengan kolom migration
5. RINGKASAN PEMBAYARAN: sesuaikan nama kolom
6. AKSI: hanya tampilkan tombol yang route-nya ada

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 4.7 — Wishlist: `resources/views/buyer/wishlist.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman wishlist buyer.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Apakah ada tabel wishlists di migration? struktur kolomnya?
 - Route untuk remove wishlist, add to cart dari wishlist]

INSTRUKSI KRITIS:
- Jika tidak ada tabel wishlist di migration, beritahu user bahwa fitur ini tidak bisa diimplementasi
- Nama kolom sesuai migration

LAYOUT: Extends dashboard layout

DESIGN:
- Header: "Wishlist Saya (X produk)"
- Grid produk: sama seperti product index tapi lebih compact
- Setiap card: tombol hapus dari wishlist
- Tombol "Tambah ke Keranjang"
- Empty state: ilustrasi hati kosong
- Select all + hapus semua (jika ada route bulk delete)

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

## 📦 FASE 5 — HALAMAN SELLER

> ⚠️ **Pastikan POST-PHASE CHECK Fase 4 sudah selesai sebelum mulai Fase 5.**

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 5:

```
SEBELUM MENULIS KODE SELLER:
1. Dari Pre-Flight 0: konfirmasi semua route names seller
2. Dari migration: struktur tabel sellers/stores, products, orders
3. Kolom status seller: pastikan nilai enum yang benar
4. Chart library: cek dari package.json apakah Chart.js atau ApexCharts sudah ada, atau perlu CDN
```

---

### PROMPT 5.1 — Seller Dashboard: `resources/views/seller/dashboard.blade.php`

```
Kamu adalah senior UI engineer. Redesign dashboard seller Laravel.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names seller
 - Struktur tabel orders, products, sellers/stores
 - Kolom revenue/pendapatan di tabel orders
 - Nilai enum status order
 - Apakah ada tabel reviews/ratings?]

INSTRUKSI KRITIS:
- Semua variabel ($totalRevenue, $todayOrders, dll) harus sesuai dengan yang dipass controller
- Chart: gunakan Chart.js dari CDN jika tidak ada di package.json
- Nama kolom pendapatan/revenue sesuai migration

LAYOUT: Extends dashboard layout (sidebar seller)

SECTIONS:

1. HEADER: Greeting + nama toko + status badge (sesuaikan dengan kolom status di migration)

2. KPI STATS: sesuaikan dengan data yang benar-benar ada di database

3. REVENUE CHART:
   - Chart.js atau ApexCharts dari CDN
   - Data dari variabel controller
   - Warna chart: #72bf77

4. TOP PRODUK TERLARIS: sesuaikan kolom dengan migration

5. PESANAN TERBARU: sesuaikan kolom status dengan enum migration

6. AKTIVITAS TERBARU: sesuaikan dengan data yang ada

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 5.2 — Produk Seller: `resources/views/seller/products/index.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman daftar produk milik seller.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names seller products (index, create, edit, delete)
 - Struktur tabel products lengkap
 - Kolom status produk dan nilai enum-nya
 - Apakah ada soft delete?]

INSTRUKSI KRITIS:
- Status toggle: gunakan nilai enum yang benar dari migration
- Route hapus: gunakan method DELETE dengan @method('DELETE')
- Kolom stok: nama kolom sesuai migration

LAYOUT: Extends dashboard layout

SECTIONS:
1. HEADER + tombol tambah produk
2. FILTER BAR: sesuaikan status filter dengan enum di migration
3. PRODUCT TABLE: kolom sesuai migration
4. EMPTY STATE

JAVASCRIPT:
- Status toggle AJAX ke route yang benar
- Delete confirmation modal (bukan browser confirm)
- Bulk action jika ada route-nya

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 5.3 — Tambah Produk: `resources/views/seller/products/create.blade.php`

```
Kamu adalah senior UI engineer. Redesign form tambah produk seller.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route untuk store product
 - Kolom-kolom di tabel products (LENGKAP dari migration)
 - Apakah ada tabel product_images yang terpisah atau gambar disimpan satu kolom?
 - Struktur tabel categories untuk select
 - Nilai enum untuk status produk]

INSTRUKSI KRITIS:
- Form field HANYA untuk kolom yang ada di tabel products migration
- Nama input harus sesuai dengan yang divalidasi di ProductRequest/Controller
- Upload gambar: sesuaikan dengan cara project menyimpan gambar (single atau multiple)
- Kategori select: data dari tabel categories

FORM LAYOUT (2 kolom desktop, 1 kolom mobile):

KIRI (main, 60%):
- Hanya field yang ada di migration

KANAN (sidebar, 40%):
- Upload Gambar (sesuaikan dengan struktur penyimpanan gambar)
- Status Produk: gunakan nilai enum yang benar
- Tombol: "Simpan Draft" + "Publikasikan"

JAVASCRIPT:
- Slug auto-generate dari nama produk (jika ada kolom slug di migration)
- Image preview
- Form validation

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 5.4 — Seller Analytics: `resources/views/seller/analytics.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman analitik seller.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route untuk analytics seller
 - Struktur tabel orders untuk kalkulasi revenue
 - Apakah controller sudah ada untuk analytics?]

INSTRUKSI KRITIS:
- Data chart sesuaikan dengan variabel yang dipass controller
- Jika controller belum ada endpoint untuk chart, gunakan placeholder data dengan komentar TODO
- Export CSV: route harus ada

LAYOUT: Extends dashboard layout

[Sections seperti sebelumnya, disesuaikan dengan data yang ada]

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 5.5 — Seller Orders: `resources/views/seller/orders/index.blade.php`

```
Kamu adalah senior UI engineer. Redesign halaman pesanan masuk seller.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names seller orders
 - Kolom status order dan nilai enum-nya
 - Route untuk proses order (update status)
 - Apakah ada kolom untuk nomor resi/tracking?]

INSTRUKSI KRITIS:
- Tab status HARUS menggunakan nilai enum yang benar
- Modal proses order: field resi sesuai dengan kolom di migration
- Route update status menggunakan method yang benar (PATCH/PUT)

LAYOUT: Extends dashboard layout

[Sections seperti sebelumnya]

MODAL KONFIRMASI:
- Field sesuai dengan kolom yang ada di migration (resi, kurir, dll)
- Action ke route yang benar dengan method yang tepat

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 5.6 — Seller Apply/Pending/Settings/Messages

```
Kamu adalah senior UI engineer. Redesign 4 halaman seller berikut.

KONTEKS PROJECT UNTUK SEMUA FILE:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk: seller.apply, seller.pending, seller.settings, seller.messages/chat
 - Struktur tabel sellers/stores lengkap
 - Kolom status seller dan nilai enum
 - Apakah ada tabel messages/chats? strukturnya?
 - Upload: apakah ada kolom untuk dokumen KTP, NPWP, logo?]

INSTRUKSI KRITIS UNTUK SETIAP FILE:
- Jika route file tertentu tidak ada di project, lewati file itu dan beritahu user
- Field form hanya untuk kolom yang ada di migration
- Sesuaikan kondisi status dengan enum di migration

FILE 1: resources/views/seller/apply.blade.php
- Form pendaftaran menjadi seller
- Field sesuai dengan kolom di tabel sellers/stores dari migration
- Upload fields hanya yang ada di migration

FILE 2: resources/views/seller/pending.blade.php
- Sesuaikan stepper dengan alur approval di project
- Status label sesuai dengan nilai enum di migration

FILE 3: resources/views/seller/settings.blade.php
- Bagian form sesuai dengan kolom di tabel sellers/stores
- Simpan per bagian dengan action ke route yang benar

FILE 4: resources/views/seller/messages.blade.php
- Sesuaikan struktur chat dengan tabel messages yang ada
- Jika tidak ada tabel messages: buat UI dengan TODO comment untuk backend

Untuk setiap file: Full Blade, Extends dashboard layout. Tidak perlu penjelasan.
```

---

## 📦 FASE 6 — HALAMAN ADMIN

> ⚠️ **Pastikan POST-PHASE CHECK Fase 5 sudah selesai sebelum mulai Fase 6.**

### INSTRUKSI WAJIB UNTUK SEMUA PROMPT FASE 6:

```
SEBELUM MENULIS KODE ADMIN:
1. Dari Pre-Flight 0: semua route admin
2. Konfirmasi cara check role admin di middleware
3. Untuk map users: apakah ada kolom lat/lng di tabel users? dari migration?
4. Untuk access codes: apakah ada tabel access_codes? strukturnya?
```

---

### PROMPT 6.1 — Admin Dashboard: `resources/views/admin/dashboard.blade.php`

```
Kamu adalah senior UI engineer. Redesign dashboard admin Laravel.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Semua route admin
 - Struktur tabel users (kolom role, status)
 - Struktur tabel sellers (kolom status)
 - Struktur tabel orders (untuk revenue)
 - Nilai enum untuk semua status yang relevan]

INSTRUKSI KRITIS:
- KPI cards: variabel sesuai dengan yang dipass controller
- Pending actions: sesuaikan status dengan nilai enum di migration
- Chart data: sesuai dengan variabel controller

LAYOUT: Extends dashboard layout (sidebar admin, dark)

[Sections seperti sebelumnya, semua disesuaikan dengan data migration]

OUTPUT: Full Blade file. Tidak perlu penjelasan.
```

---

### PROMPT 6.2 — Admin Users & Verifications

```
Kamu adalah senior UI engineer. Redesign 4 halaman admin berikut.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk semua halaman ini
 - Struktur tabel users lengkap
 - Struktur tabel sellers lengkap
 - Struktur tabel verifications (jika ada)
 - Nilai enum: role user, status user, status seller, status verifikasi]

INSTRUKSI KRITIS UNTUK SEMUA FILE:
- Kolom tabel sesuai migration
- Status badge menggunakan nilai enum yang benar
- Route untuk approve/reject/ban harus menggunakan method yang tepat (POST/PATCH)
- @method() directive sesuai

FILE 1: resources/views/admin/users.blade.php
- Kolom: sesuaikan dengan tabel users dari migration
- Filter role: gunakan nilai enum role yang benar
- Aksi: sesuaikan dengan route yang ada

FILE 2: resources/views/admin/sellers.blade.php
- Kolom: sesuaikan dengan tabel sellers dari migration
- Status: gunakan nilai enum yang benar

FILE 3: resources/views/admin/users-verification.blade.php
- Sesuaikan dengan tabel/kolom verifikasi yang ada di project
- Jika tidak ada tabel terpisah: sesuaikan dengan kolom di tabel users

FILE 4: resources/views/admin/verifications.blade.php
- Sesuaikan dengan struktur data verifikasi yang ada

Untuk setiap file: Full Blade, Extends dashboard layout. Tidak perlu penjelasan.
```

---

### PROMPT 6.3 — Admin Map & Access Codes

```
Kamu adalah senior UI engineer. Redesign 2 halaman admin berikut.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route untuk map-users dan access-codes
 - Apakah ada kolom lat/lng di tabel users? nama kolomnya?
 - Apakah ada tabel access_codes? struktur kolomnya?
 - Nilai enum untuk status dan jenis access code]

INSTRUKSI KRITIS:
- Map: HANYA buat fitur ini jika ada kolom lat/lng di tabel users
  Jika tidak ada: buat halaman dengan pesan "Fitur peta membutuhkan kolom koordinat di tabel users" + instruksi
- Access codes: HANYA buat fitur ini jika ada tabel access_codes di migration
  Jika tidak ada: buat halaman placeholder dengan TODO

FILE 1: resources/views/admin/map-users.blade.php
- Map container: Leaflet.js dari CDN
- Data marker: const markers = @json($users) — sesuaikan select kolom dengan migration
- Popup marker: gunakan kolom yang ada di migration

FILE 2: resources/views/admin/access-codes.blade.php
- Kolom tabel sesuai migration
- Status dan jenis: gunakan nilai enum dari migration
- Generate modal: field sesuai dengan yang dihandle controller

Untuk setiap file: Full Blade, Extends dashboard layout. Tidak perlu penjelasan.
```

---

## 📦 FASE 7 — HALAMAN SHARED & PROFIL

> ⚠️ **Pastikan POST-PHASE CHECK Fase 6 sudah selesai sebelum mulai Fase 7.**

### PROMPT 7.1 — Profile, Categories, Chat

```
Kamu adalah senior UI engineer. Redesign 4 halaman berikut.

KONTEKS PROJECT:
[PASTE DARI PRE-FLIGHT 0:
 - Route names untuk profile, categories, chat
 - Struktur tabel users (kolom yang bisa diupdate)
 - Struktur tabel categories
 - Struktur tabel messages/chats (jika ada)
 - Apakah ada fitur notifikasi? tabel notifications?]

INSTRUKSI KRITIS UNTUK SEMUA FILE:
- Field form profile: hanya kolom yang ada di migration dan dihandle controller
- Categories: sesuaikan kolom dengan migration
- Chat: sesuaikan dengan struktur tabel messages yang ada
- Semua route() harus yang benar dari Pre-Flight 0

FILE 1: resources/views/profile.blade.php
- Tab Informasi: hanya field yang ada di tabel users migration
- Tab Keamanan: ubah password form
- Tab Notifikasi: hanya jika ada tabel/kolom notifikasi settings

FILE 2: resources/views/categories/index.blade.php
- Kolom kategori sesuai migration (nama, icon, slug, dll)
- Link ke kategori: route yang benar

FILE 3: resources/views/categories/show.blade.php
- Produk dalam kategori: sesuaikan dengan relasi di model

FILE 4: resources/views/chat/show.blade.php
- Struktur chat: sesuaikan dengan tabel messages di migration
- Jika tidak ada tabel chat: buat UI dengan komentar TODO

Untuk setiap file: Full Blade. Tidak perlu penjelasan.
```

---

## 📋 URUTAN PENGERJAAN YANG DISARANKAN

```
PRE-FLIGHT (WAJIB PERTAMA — JANGAN SKIP):
└── PRE-FLIGHT 0  → Analisis & Evaluasi Project Lengkap

PRIORITAS 1 (Foundation — WAJIB KEDUA):
├── PROMPT 1.1  → Design System CSS
├── PROMPT 1.2  → Layout app.blade.php
├── PROMPT 1.3  → Layout dashboard.blade.php
├── PROMPT 2.1  → Sidebar Admin
├── PROMPT 2.2  → Sidebar Buyer
└── PROMPT 2.3  → Sidebar Seller
[→ JALANKAN POST-PHASE CHECK sebelum lanjut]

PRIORITAS 2 (Auth — Public Facing):
├── PROMPT 3.1  → Welcome/Landing Page
├── PROMPT 3.2  → Login
├── PROMPT 3.3  → Register
├── PROMPT 3.4  → Verify Phone
└── PROMPT 3.5  → Verify Admin Approval
[→ JALANKAN POST-PHASE CHECK sebelum lanjut]

PRIORITAS 3 (Buyer Flow):
├── PROMPT 4.1  → Buyer Dashboard
├── PROMPT 4.2  → Products Index
├── PROMPT 4.3  → Product Show
├── PROMPT 4.4  → Cart
├── PROMPT 4.5  → Orders Index
├── PROMPT 4.6  → Order Show
└── PROMPT 4.7  → Wishlist
[→ JALANKAN POST-PHASE CHECK sebelum lanjut]

PRIORITAS 4 (Seller Flow):
├── PROMPT 5.1  → Seller Dashboard
├── PROMPT 5.2  → Products Index
├── PROMPT 5.3  → Create Product
├── PROMPT 5.4  → Analytics
├── PROMPT 5.5  → Orders
└── PROMPT 5.6  → Apply/Pending/Settings/Messages
[→ JALANKAN POST-PHASE CHECK sebelum lanjut]

PRIORITAS 5 (Admin):
├── PROMPT 6.1  → Admin Dashboard
├── PROMPT 6.2  → Users & Verifications
└── PROMPT 6.3  → Map & Access Codes
[→ JALANKAN POST-PHASE CHECK sebelum lanjut]

PRIORITAS 6 (Shared):
└── PROMPT 7.1  → Profile, Categories, Chat
[→ JALANKAN POST-PHASE CHECK FINAL]
```

---

## ⚠️ TIPS PENGGUNAAN PROMPT INI

1. **SELALU MULAI DENGAN PRE-FLIGHT 0** — Ini bukan opsional. Tanpa analisis ini, AI akan menebak nama route dan kolom, dan akan banyak error.

2. **Paste hasil Pre-Flight 0 ke setiap prompt** — Di setiap prompt ada bagian `[PASTE DARI PRE-FLIGHT 0...]`. ISI BAGIAN INI. Jangan dibiarkan kosong.

3. **Selalu sertakan file asli** — Paste isi file Blade aslimu di bawah prompt, tambahkan `=== FILE ASLI ===` sebagai separator

4. **Sertakan context teknis lengkap** — Tambahkan di atas setiap prompt:
   ```
   Tech stack: Laravel [versi], Tailwind CSS [versi], Alpine.js [versi]
   Dari Pre-Flight 0:
   - Route names yang relevan: [paste di sini]
   - Kolom migration yang relevan: [paste di sini]
   - Nilai enum yang relevan: [paste di sini]
   ```

5. **Jalankan POST-PHASE CHECK** — Setelah setiap fase, cek semua output sebelum lanjut ke fase berikutnya.

6. **Iterasi bertahap** — 1 prompt = 1 file atau 1 grup kecil. Jangan submit semua sekaligus.

7. **Verifikasi manual setiap route()** — Buka web.php, pastikan setiap route name yang dipakai di Blade benar-benar ada.

8. **Test di browser setelah setiap fase** — Jangan tunggu semua selesai baru test. Test per fase.

9. **Konsistensi warna** — Pastikan semua output menggunakan `#72bf77` sebagai primary.

---

## 🎯 CHECKLIST KUALITAS PER FILE

Sebelum deploy, pastikan setiap file:

**Backend & Laravel:**
- [ ] Tidak mengubah backend logic (Controller, Model, Migration)
- [ ] Blade syntax valid (`@extends`, `@section`, `@yield`, `@csrf`, `@method`, dll)
- [ ] Semua `route()` helper menggunakan nama yang ADA di web.php (verified dari Pre-Flight 0)
- [ ] Nama kolom dalam template sesuai dengan migration (tidak ada typo atau tebakan)
- [ ] Nilai enum (status, role, dll) sesuai dengan yang ada di migration
- [ ] `old('field')` menggunakan nama field yang benar
- [ ] `@error('field')` menggunakan nama field yang benar
- [ ] Method form benar (GET/POST/PUT/PATCH/DELETE + @method directive)

**Frontend:**
- [ ] Responsive di mobile (375px), tablet (768px), desktop (1280px)
- [ ] Color primary `#72bf77` konsisten
- [ ] Font Plus Jakarta Sans + DM Sans terload
- [ ] Animasi/transition ada (minimal hover states)
- [ ] Empty state tersedia (bukan halaman kosong)
- [ ] Flash message / toast terintegrasi
- [ ] Loading state ada (skeleton atau spinner)
- [ ] Form memiliki validasi visual
- [ ] Glass effect pada elemen yang sesuai

**Kualitas:**
- [ ] Tidak ada Bootstrap default look
- [ ] Terasa seperti produk SaaS nyata, bukan template
- [ ] Tidak ada hardcoded data yang tidak dibungkus `{{ $var ?? 'default' }}`
- [ ] Tidak ada route yang hardcoded URL (semua pakai `route()`)
- [ ] Tidak ada fitur UI yang tidak didukung data dari database

---

*Plan ini dibuat untuk upgrade UI/UX Laravel Marketplace Project ke standar SaaS Modern 2026*  
*Primary Color: #72bf77 | Design System: Glass + Minimal + Premium*  
*Dilengkapi dengan: Pre-Flight Analysis, Route Mapping, Database Validation, Post-Phase Quality Check*