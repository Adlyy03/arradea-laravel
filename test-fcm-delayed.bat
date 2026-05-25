@echo off
echo ================================================================================
echo   FCM TEST WITH DELAY
echo ================================================================================
echo.
echo This script will wait 5 seconds before sending notification.
echo.
echo Instructions:
echo   1. Keep browser tab ACTIVE (don't switch to this window)
echo   2. Wait for notification to appear in browser
echo.
echo Starting in 5 seconds...
echo.
timeout /t 5 /nobreak
echo.
echo Sending notification NOW...
php test-fcm-final.php
echo.
pause
