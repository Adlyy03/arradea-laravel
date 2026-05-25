@echo off
echo.
echo ========================================
echo   CHECKING CHAT NOTIFICATION LOGS
echo ========================================
echo.
powershell -Command "Get-Content storage/logs/laravel.log -Tail 150 | Select-String -Pattern 'CHAT MESSAGE|Attempting to send|Recipient|FCM tokens|Push notification|chat_message' -Context 1"
echo.
echo ========================================
echo   Press any key to exit
echo ========================================
pause > nul
