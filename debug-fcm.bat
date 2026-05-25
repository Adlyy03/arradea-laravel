@echo off
echo ========================================
echo FCM DEBUG TOOL
echo ========================================
echo.
echo Opening debug page...
echo.
echo LANGKAH-LANGKAH:
echo 1. Klik "1. Initialize FCM"
echo 2. Tunggu sampai selesai
echo 3. Klik "2. Send Test Notification"
echo 4. Klik "3. Test Manual Notification" untuk test browser
echo.
echo ========================================
start http://localhost:8000/fcm-debug.html
