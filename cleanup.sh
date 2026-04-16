#!/bin/bash
# Cleanup script for testing files

cd "C:\laragon\www\arradeaaaa" || exit 1

echo "Deleting testing and documentation files..."
echo ""

# Root directory files
rm -f "00_READ_ME_FIRST.txt"
rm -f "FINAL_SUMMARY.txt"
rm -f "QUICK_START.txt"
rm -f "README_SEEDER.md"
rm -f "SEEDER_DOCUMENTATION.md"
rm -f "SETUP_INSTRUCTIONS.txt"
rm -f "VARIANT_UI_GUIDE.txt"
rm -f "VARIANT_UI_IMPROVEMENTS.md"
rm -f "run_seeder.bat"
rm -f "run_seeder_test.php"
rm -f "test_abiyu_seeder.php"
rm -f "verify_seeder.php"

# Subdirectory files
rm -f "database/seeders/AbiuFoodProductSeeder.php"
rm -f "app/Http/Controllers/TestController.php"
rm -f "public/test-seeder.html"
rm -f "public/index-seeder.html"

echo "✅ Cleanup complete!"
echo ""
echo "Deleted files:"
echo "- 12 documentation/testing files from root"
echo "- 1 seeder file"
echo "- 1 test controller"
echo "- 2 testing HTML files"
echo ""
echo "Total: 16 files deleted"
