# 🚀 Cara Jalankan Project Arradea

## 📋 Requirements
- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL/SQLite

## ⚡ Quick Start

### 1. Clone & Install
```bash
git clone <repo-url>
cd arradeaaaa
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=sqlite
# atau MySQL:
# DB_CONNECTION=mysql
# DB_DATABASE=arradea
# DB_USERNAME=root
# DB_PASSWORD=
```

### 3. Database
```bash
# SQLite (otomatis):
touch database/database.sqlite

# Atau MySQL:
# Buat database dulu di phpMyAdmin/MySQL

php artisan migrate
php artisan db:seed  # (optional)
```

### 4. Jalankan
```bash
# Development:
php artisan serve
npm run dev

# Atau pakai Laragon:
# Langsung buka di browser
```

### 5. Akses
- **Web:** http://localhost:8000
- **Admin:** Daftar user pertama jadi admin otomatis

## 🎯 Fitur Utama
- ✅ Buyer/Seller mode auto-switch
- ✅ Store auto buka/tutup (schedule)
- ✅ PWA (installable app)
- ✅ Real-time chat
- ✅ Export Excel

## 🔧 Commands
```bash
# Sync store schedule (auto buka/tutup):
php artisan schedule:work

# Queue (untuk notifikasi):
php artisan queue:work

# Clear cache:
php artisan optimize:clear
```

## 📱 PWA Setup
1. Generate icons: `/generate-icons.html`
2. Deploy dengan HTTPS
3. Install ke home screen

---

**Done!** 🎉 Project siap dipakai.
