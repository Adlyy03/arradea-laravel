@echo off
REM Arradea Marketplace - Development Helper Script (Windows)
REM Usage: dev.bat [command]

if "%1"=="start" goto start
if "%1"=="build" goto build
if "%1"=="fresh" goto fresh
if "%1"=="cache" goto cache
if "%1"=="optimize" goto optimize
if "%1"=="test" goto test
if "%1"=="format" goto format
if "%1"=="install" goto install
goto help

:start
echo 🚀 Starting Arradea Marketplace...
echo.
echo Starting Vite dev server...
start cmd /k "npm run dev"
echo.
echo Starting Laravel server...
php artisan serve
goto end

:build
echo 🔨 Building assets for production...
npm run build
echo ✅ Build complete!
goto end

:fresh
echo 🔄 Fresh installation...
php artisan migrate:fresh --seed
echo ✅ Database refreshed!
goto end

:cache
echo 🧹 Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo ✅ Cache cleared!
goto end

:optimize
echo ⚡ Optimizing for production...
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ✅ Optimization complete!
goto end

:test
echo 🧪 Running tests...
php artisan test
goto end

:format
echo ✨ Formatting code...
vendor\bin\pint
echo ✅ Code formatted!
goto end

:install
echo 📦 Installing dependencies...
composer install
npm install
copy .env.example .env
php artisan key:generate
echo ✅ Installation complete!
echo.
echo Next steps:
echo 1. Configure database in .env
echo 2. Run: dev.bat fresh
echo 3. Run: dev.bat start
goto end

:help
echo Arradea Marketplace - Development Helper
echo.
echo Usage: dev.bat [command]
echo.
echo Commands:
echo   start     - Start development servers (Vite + Laravel)
echo   build     - Build assets for production
echo   fresh     - Fresh database migration + seed
echo   cache     - Clear all caches
echo   optimize  - Optimize for production
echo   test      - Run tests
echo   format    - Format code with Pint
echo   install   - Install dependencies
echo.
goto end

:end
