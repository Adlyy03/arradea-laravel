@echo off
cls
color 0A
echo.
echo ========================================
echo    FCM NOTIFICATION - FIX SEKARANG
echo ========================================
echo.
echo MASALAH: Notifikasi tidak muncul
echo SOLUSI: 3 langkah simple!
echo.
echo ========================================
echo    LANGKAH-LANGKAH (SIMPLE!)
echo ========================================
echo.
echo STEP 1: Klik "Izinkan Notifikasi"
echo         - Browser akan minta permission
echo         - Klik "ALLOW" / "IZINKAN"
echo.
echo STEP 2: Klik "Test Notifikasi Manual"
echo         - Notifikasi HARUS muncul
echo         - Jika tidak muncul = masalah di Windows
echo.
echo STEP 3: Klik "Kirim Notifikasi FCM"
echo         - Notifikasi dari server
echo         - Notifikasi HARUS muncul
echo.
echo ========================================
echo.
echo Tekan ENTER untuk mulai...
pause > nul

start http://localhost:8000/fcm-simple.html

echo.
echo ========================================
echo Browser sudah terbuka!
echo ========================================
echo.
echo Ikuti 3 langkah di atas.
echo.
echo Jika Step 2 tidak muncul:
echo - Buka Windows Settings
echo - System ^> Notifications
echo - Pastikan Chrome notifications ON
echo.
echo ========================================
echo.
pause
