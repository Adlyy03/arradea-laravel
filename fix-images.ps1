# ============================================================================
# Arradea Marketplace - Image Fix Script (PowerShell)
# ============================================================================
# Script untuk memperbaiki masalah gambar produk tidak muncul
# 
# Usage:
#   .\fix-images.ps1
#   atau
#   powershell -ExecutionPolicy Bypass -File fix-images.ps1
# ============================================================================

Write-Host "🖼️  Arradea Marketplace - Image Fix Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Create storage link
Write-Host "📁 Step 1: Creating storage link..." -ForegroundColor Yellow
if (Test-Path "public\storage") {
    Write-Host "⚠️  Storage link already exists" -ForegroundColor Yellow
    $response = Read-Host "Remove and recreate? (y/n)"
    if ($response -eq "y" -or $response -eq "Y") {
        Remove-Item "public\storage" -Force -Recurse -ErrorAction SilentlyContinue
        php artisan storage:link
        Write-Host "✅ Storage link recreated" -ForegroundColor Green
    }
} else {
    php artisan storage:link
    Write-Host "✅ Storage link created" -ForegroundColor Green
}
Write-Host ""

# Step 2: Create necessary folders
Write-Host "📂 Step 2: Creating storage folders..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "storage\app\public\products" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\app\public\categories" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\app\public\payments" | Out-Null
Write-Host "✅ Folders created" -ForegroundColor Green
Write-Host ""

# Step 3: Clear cache
Write-Host "🧹 Step 3: Clearing cache..." -ForegroundColor Yellow
php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "✅ Cache cleared" -ForegroundColor Green
Write-Host ""

# Step 4: Check database image paths
Write-Host "🔍 Step 4: Checking database..." -ForegroundColor Yellow
$checkScript = @"
`$count = \App\Models\Product::whereNotNull('image')
    ->where('image', 'NOT LIKE', 'http%')
    ->where('image', 'NOT LIKE', '/storage/%')
    ->count();
if (`$count > 0) {
    echo '⚠️  Found ' . `$count . ' products with incorrect image paths' . PHP_EOL;
    echo 'Run this SQL to fix:' . PHP_EOL;
    echo 'UPDATE products SET image = CONCAT(\"/storage/\", image) WHERE image NOT LIKE \"http%\" AND image NOT LIKE \"/storage/%\" AND image IS NOT NULL;' . PHP_EOL;
} else {
    echo '✅ All image paths look correct' . PHP_EOL;
}
"@

php artisan tinker --execute="$checkScript"
Write-Host ""

# Step 5: Test storage access
Write-Host "🧪 Step 5: Testing storage access..." -ForegroundColor Yellow
if (Test-Path "storage\app\public\products") {
    $fileCount = (Get-ChildItem "storage\app\public\products" -ErrorAction SilentlyContinue).Count
    Write-Host "✅ Products folder accessible ($fileCount files)" -ForegroundColor Green
} else {
    Write-Host "❌ Products folder not accessible" -ForegroundColor Red
}
Write-Host ""

# Step 6: Rebuild frontend (optional)
Write-Host "🏗️  Step 6: Rebuild frontend?" -ForegroundColor Yellow
$response = Read-Host "Run 'npm run build'? (y/n)"
if ($response -eq "y" -or $response -eq "Y") {
    npm run build
    Write-Host "✅ Frontend rebuilt" -ForegroundColor Green
} else {
    Write-Host "⏭️  Skipped" -ForegroundColor Gray
}
Write-Host ""

# Summary
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✅ Image fix script completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Test upload gambar baru di /seller/products/create"
Write-Host "2. Check apakah gambar lama sudah muncul"
Write-Host "3. Jika masih ada masalah, baca TROUBLESHOOTING_IMAGES.md"
Write-Host ""
Write-Host "Quick test:" -ForegroundColor Cyan
Write-Host "  php artisan tinker"
Write-Host "  >>> \App\Models\Product::latest()->first()->image"
Write-Host ""
