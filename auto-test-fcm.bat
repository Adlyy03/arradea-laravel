@echo off
echo ================================================================================
echo   AUTOMATIC FCM TEST
echo ================================================================================
echo.

echo Step 1: Running backend test...
echo --------------------------------------------------------------------------------
php test-fcm-comprehensive.php
echo.

echo Step 2: Opening frontend test page...
echo --------------------------------------------------------------------------------
echo Opening: http://localhost:8000/test-fcm-frontend.html
start http://localhost:8000/test-fcm-frontend.html
echo.

echo ================================================================================
echo   INSTRUCTIONS
echo ================================================================================
echo.
echo In the browser window that just opened:
echo   1. Click "Fix Service Worker" (if needed)
echo   2. Click "Request Permission" (if needed)
echo   3. Click "Test Manual Notification"
echo   4. Click "Test SW Notification"
echo   5. Click "Initialize Firebase"
echo   6. Click "Send Test from Backend"
echo.
echo All notifications should appear in your browser!
echo.
echo ================================================================================
pause
