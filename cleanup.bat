@echo off
REM Cleanup script for testing files

cd /d C:\laragon\www\arradeaaaa

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║             Cleaning up Testing Files...                      ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

REM Root directory files
echo Deleting root files...
del /F /Q "00_READ_ME_FIRST.txt" >nul 2>&1
del /F /Q "FINAL_SUMMARY.txt" >nul 2>&1
del /F /Q "QUICK_START.txt" >nul 2>&1
del /F /Q "README_SEEDER.md" >nul 2>&1
del /F /Q "SEEDER_DOCUMENTATION.md" >nul 2>&1
del /F /Q "SETUP_INSTRUCTIONS.txt" >nul 2>&1
del /F /Q "VARIANT_UI_GUIDE.txt" >nul 2>&1
del /F /Q "VARIANT_UI_IMPROVEMENTS.md" >nul 2>&1
del /F /Q "run_seeder.bat" >nul 2>&1
del /F /Q "run_seeder_test.php" >nul 2>&1
del /F /Q "test_abiyu_seeder.php" >nul 2>&1
del /F /Q "verify_seeder.php" >nul 2>&1

REM Subdirectory files
echo Deleting seeder files...
del /F /Q "database\seeders\AbiuFoodProductSeeder.php" >nul 2>&1

echo Deleting test controller...
del /F /Q "app\Http\Controllers\TestController.php" >nul 2>&1

echo Deleting test HTML files...
del /F /Q "public\test-seeder.html" >nul 2>&1
del /F /Q "public\index-seeder.html" >nul 2>&1

echo.
echo ✅ Cleanup complete!
echo.
echo Deleted files:
echo - 12 documentation/testing files from root
echo - 1 seeder file (database/seeders/AbiuFoodProductSeeder.php)
echo - 1 test controller (app/Http/Controllers/TestController.php)
echo - 2 testing HTML files (public/test-seeder.html, public/index-seeder.html)
echo.
echo Total: 16 files deleted
echo.
echo routes/api.php sudah dibersihkan (TestController import & route dihapus)
echo.
pause
