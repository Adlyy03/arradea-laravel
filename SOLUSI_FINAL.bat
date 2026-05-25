@echo off
cls
color 0C
echo.
echo ========================================
echo    SOLUSI FINAL - TOKEN MISMATCH
echo ========================================
echo.
echo MASALAH TERIDENTIFIKASI:
echo - Step 2 (manual) MUNCUL = Browser OK
echo - Step 3 (FCM) TIDAK MUNCUL = Token salah
echo.
echo ROOT CAUSE:
echo Backend kirim ke TOKEN LAMA
echo Browser punya TOKEN BARU
echo.
echo ========================================
echo    SOLUSI: FORCE REFRESH TOKEN
echo ========================================
echo.
echo Script ini akan:
echo 1. Hapus semua service worker lama
echo 2. Register service worker baru
echo 3. Generate TOKEN BARU
echo 4. Save token ke database
echo 5. Kirim notifikasi ke TOKEN BARU
echo.
echo Notifikasi PASTI muncul!
echo.
echo ========================================
echo.
echo Tekan ENTER untuk mulai...
pause > nul

start http://localhost:8000/fcm-force-refresh.html

echo.
echo ========================================
echo Browser sudah terbuka!
echo ========================================
echo.
echo Di browser:
echo 1. Klik tombol besar
echo 2. Tunggu proses selesai
echo 3. Notifikasi AKAN MUNCUL!
echo.
echo ========================================
echo.
pause
