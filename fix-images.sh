#!/bin/bash

# ============================================================================
# Arradea Marketplace - Image Fix Script
# ============================================================================
# Script untuk memperbaiki masalah gambar produk tidak muncul
# 
# Usage:
#   bash fix-images.sh
#   atau
#   chmod +x fix-images.sh && ./fix-images.sh
# ============================================================================

echo "🖼️  Arradea Marketplace - Image Fix Script"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Create storage link
echo "📁 Step 1: Creating storage link..."
if [ -L "public/storage" ]; then
    echo -e "${YELLOW}⚠️  Storage link already exists${NC}"
    read -p "Remove and recreate? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf public/storage
        php artisan storage:link
        echo -e "${GREEN}✅ Storage link recreated${NC}"
    fi
else
    php artisan storage:link
    echo -e "${GREEN}✅ Storage link created${NC}"
fi
echo ""

# Step 2: Create necessary folders
echo "📂 Step 2: Creating storage folders..."
mkdir -p storage/app/public/products
mkdir -p storage/app/public/categories
mkdir -p storage/app/public/payments
echo -e "${GREEN}✅ Folders created${NC}"
echo ""

# Step 3: Set permissions (Linux/Mac only)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    echo "🔐 Step 3: Setting permissions..."
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    echo -e "${GREEN}✅ Permissions set${NC}"
    echo ""
else
    echo "⏭️  Step 3: Skipped (Windows detected)"
    echo ""
fi

# Step 4: Clear cache
echo "🧹 Step 4: Clearing cache..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo -e "${GREEN}✅ Cache cleared${NC}"
echo ""

# Step 5: Check database image paths
echo "🔍 Step 5: Checking database..."
php artisan tinker --execute="
\$count = \App\Models\Product::whereNotNull('image')
    ->where('image', 'NOT LIKE', 'http%')
    ->where('image', 'NOT LIKE', '/storage/%')
    ->count();
if (\$count > 0) {
    echo '⚠️  Found ' . \$count . ' products with incorrect image paths\n';
    echo 'Run this SQL to fix:\n';
    echo 'UPDATE products SET image = CONCAT(\"/storage/\", image) WHERE image NOT LIKE \"http%\" AND image NOT LIKE \"/storage/%\" AND image IS NOT NULL;\n';
} else {
    echo '✅ All image paths look correct\n';
}
"
echo ""

# Step 6: Test storage access
echo "🧪 Step 6: Testing storage access..."
if [ -d "storage/app/public/products" ]; then
    file_count=$(ls -1 storage/app/public/products 2>/dev/null | wc -l)
    echo -e "${GREEN}✅ Products folder accessible (${file_count} files)${NC}"
else
    echo -e "${RED}❌ Products folder not accessible${NC}"
fi
echo ""

# Step 7: Rebuild frontend (optional)
echo "🏗️  Step 7: Rebuild frontend?"
read -p "Run 'npm run build'? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    npm run build
    echo -e "${GREEN}✅ Frontend rebuilt${NC}"
else
    echo "⏭️  Skipped"
fi
echo ""

# Summary
echo "=========================================="
echo -e "${GREEN}✅ Image fix script completed!${NC}"
echo ""
echo "Next steps:"
echo "1. Test upload gambar baru di /seller/products/create"
echo "2. Check apakah gambar lama sudah muncul"
echo "3. Jika masih ada masalah, baca TROUBLESHOOTING_IMAGES.md"
echo ""
echo "Quick test:"
echo "  php artisan tinker"
echo "  >>> \App\Models\Product::latest()->first()->image"
echo ""
