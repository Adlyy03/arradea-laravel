@echo off
echo.
echo ========================================
echo   BUILDING ASSETS WITH ENHANCED LOGGING
echo ========================================
echo.
echo Building...
call npm run build
echo.
echo ========================================
echo   BUILD COMPLETE
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Login sebagai SELLER (Reza)
echo 2. Open browser console (F12)
echo 3. Look for FCM logs with [FCM] prefix
echo 4. Allow notification when prompted
echo 5. Check for: "FCM token berhasil disimpan"
echo 6. Verify with: php check-fcm-tokens.php
echo.
echo ========================================
pause
