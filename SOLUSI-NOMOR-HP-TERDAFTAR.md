# 🔧 Solusi: Nomor HP Sudah Terdaftar Tapi Belum Verifikasi

## 📋 Masalah
User mendaftar dengan nomor HP → Nomor masuk database → User tutup browser sebelum verifikasi OTP → Nomor HP "terpendam" di database → User tidak bisa daftar lagi karena nomor sudah ada di DB.

## ✅ Solusi yang Diimplementasikan

### 1. **Auto Re-registration untuk User Belum Verifikasi**
**File**: `app/Http/Controllers/AuthWebController.php`

**Cara Kerja**:
- Saat user daftar dengan nomor HP yang sudah ada di database
- System cek: apakah user tersebut sudah verifikasi phone (`phone_verified_at`)?
- **Jika BELUM verifikasi**: User lama otomatis dihapus, user baru bisa daftar
- **Jika SUDAH verifikasi**: Tolak registrasi, suruh login

**Keuntungan**:
- User bisa langsung daftar ulang tanpa menunggu
- Tidak perlu manual cleanup
- User experience lebih baik

### 2. **Auto-Cleanup Scheduled Task**
**File**: `app/Console/Commands/CleanupUnverifiedUsers.php`

**Cara Kerja**:
- Setiap hari jam 2 pagi, system otomatis hapus user yang:
  - Belum verifikasi phone (`phone_verified_at` = null)
  - Bukan admin
  - Sudah lebih dari 24 jam sejak registrasi

**Schedule**: `routes/console.php`
```php
Schedule::command('users:cleanup-unverified --hours=24')->dailyAt('02:00');
```

**Keuntungan**:
- Database tetap bersih
- Tidak ada "sampah" user yang menumpuk
- Otomatis berjalan tanpa intervensi manual

### 3. **Manual Cleanup Command**
**Command**: `php artisan users:cleanup-unverified`

**Options**:
- `--hours=24` : Hapus user yang belum verifikasi lebih dari X jam (default: 24)

**Contoh Penggunaan**:
```bash
# Hapus user yang belum verifikasi lebih dari 24 jam
php artisan users:cleanup-unverified

# Hapus user yang belum verifikasi lebih dari 48 jam
php artisan users:cleanup-unverified --hours=48

# Hapus user yang belum verifikasi lebih dari 1 jam (untuk testing)
php artisan users:cleanup-unverified --hours=1
```

**Keuntungan**:
- Admin bisa manual cleanup kapan saja
- Bisa adjust waktu sesuai kebutuhan
- Ada konfirmasi sebelum delete

## 🎯 Flow Lengkap

### Scenario 1: User Daftar Pertama Kali
```
1. User input nama, HP, password
2. System cek: HP belum ada di DB
3. User dibuat, OTP dikirim
4. User verifikasi OTP
5. ✅ Registrasi selesai
```

### Scenario 2: User Daftar Ulang (Belum Verifikasi)
```
1. User input nama, HP, password
2. System cek: HP sudah ada, tapi phone_verified_at = null
3. System hapus user lama otomatis
4. User baru dibuat, OTP dikirim
5. User verifikasi OTP
6. ✅ Registrasi selesai
```

### Scenario 3: User Daftar dengan HP yang Sudah Terverifikasi
```
1. User input nama, HP, password
2. System cek: HP sudah ada DAN phone_verified_at != null
3. ❌ Tolak registrasi
4. Pesan: "Nomor HP sudah terdaftar dan terverifikasi. Silakan login."
```

### Scenario 4: Auto-Cleanup (Background)
```
Setiap hari jam 2 pagi:
1. System cari user dengan:
   - phone_verified_at = null
   - role != admin
   - created_at < 24 jam yang lalu
2. Hapus semua user yang memenuhi kriteria
3. Database bersih
```

## 🔒 Keamanan

### Yang Dilindungi:
- ✅ User yang sudah verifikasi phone tidak bisa ditimpa
- ✅ Admin tidak akan pernah dihapus
- ✅ Ada konfirmasi sebelum manual cleanup
- ✅ Log activity untuk tracking

### Yang Tidak Dilindungi:
- ❌ User yang belum verifikasi bisa ditimpa (ini memang tujuannya)

## 📊 Monitoring

### Cek User Belum Verifikasi:
```sql
SELECT id, name, phone, created_at 
FROM users 
WHERE phone_verified_at IS NULL 
AND role != 'admin'
ORDER BY created_at DESC;
```

### Cek Berapa User yang Akan Dihapus:
```bash
php artisan users:cleanup-unverified --hours=24
# Akan tampil list user sebelum konfirmasi delete
```

## 🚀 Testing

### Test Manual Cleanup:
```bash
# 1. Buat user test yang belum verifikasi
# 2. Tunggu 1 jam (atau ubah created_at manual di DB)
# 3. Run command
php artisan users:cleanup-unverified --hours=1

# 4. Cek apakah user terhapus
```

### Test Re-registration:
```bash
# 1. Daftar dengan HP: 081234567890
# 2. Jangan verifikasi OTP, tutup browser
# 3. Daftar lagi dengan HP yang sama
# 4. Seharusnya berhasil, user lama otomatis terhapus
```

## ⚙️ Konfigurasi

### Ubah Waktu Auto-Cleanup:
Edit `routes/console.php`:
```php
// Dari 24 jam jadi 48 jam
Schedule::command('users:cleanup-unverified --hours=48')->dailyAt('02:00');

// Atau ubah jam eksekusi
Schedule::command('users:cleanup-unverified --hours=24')->dailyAt('03:00');
```

### Disable Auto-Cleanup:
Comment line di `routes/console.php`:
```php
// Schedule::command('users:cleanup-unverified --hours=24')->dailyAt('02:00');
```

## 📝 Catatan Penting

1. **Scheduler Harus Aktif**: Pastikan Laravel scheduler berjalan
   ```bash
   # Tambahkan ke crontab (Linux/Mac)
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   
   # Atau jalankan manual untuk testing
   php artisan schedule:work
   ```

2. **Database Backup**: Selalu backup database sebelum cleanup massal

3. **Production**: Test dulu di staging sebelum deploy ke production

4. **Monitoring**: Monitor log untuk memastikan cleanup berjalan dengan baik

## 🎉 Kesimpulan

Dengan 3 solusi ini:
- ✅ User bisa daftar ulang kapan saja jika belum verifikasi
- ✅ Database tetap bersih dengan auto-cleanup
- ✅ Admin punya kontrol manual jika diperlukan
- ✅ User experience lebih baik
- ✅ Tidak ada nomor HP yang "terpendam"

**Problem Solved!** 🚀
