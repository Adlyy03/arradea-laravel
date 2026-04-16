@echo off
REM =====================================================
REM Abiyu Food Product Seeder Script
REM =====================================================
echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║    ABIYU FOOD PRODUCT SEEDER - SETUP SCRIPT                   ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

cd /d C:\laragon\www\arradeaaaa

echo [1/3] Running seeder...
php artisan db:seed --class=AbiuFoodProductSeeder

if errorlevel 1 (
    echo.
    echo ❌ Seeder failed!
    pause
    exit /b 1
)

echo.
echo [2/3] Verifying data...
php artisan tinker --execute "
$user = App\Models\User::where('email', 'abiyu@arradea.com')->with('store.products.category')->first();
if ($user && $user->store) {
    echo PHP_EOL . '╔════════════════════════════════════════════════════════════════╗' . PHP_EOL;
    echo '║                    ✅ SEEDER SUCCESS                         ║' . PHP_EOL;
    echo '╚════════════════════════════════════════════════════════════════╝' . PHP_EOL . PHP_EOL;
    echo '📦 SELLER INFORMATION' . PHP_EOL;
    echo '─' . str_repeat('─', 60) . PHP_EOL;
    echo 'Name     : ' . $user->name . PHP_EOL;
    echo 'Email    : ' . $user->email . PHP_EOL;
    echo 'Role     : ' . $user->role . PHP_EOL . PHP_EOL;
    echo '🏪 STORE INFORMATION' . PHP_EOL;
    echo '─' . str_repeat('─', 60) . PHP_EOL;
    echo 'Store    : ' . $user->store->name . PHP_EOL;
    echo 'Address  : ' . $user->store->address . PHP_EOL . PHP_EOL;
    echo '🛍️  PRODUCTS & VARIANTS' . PHP_EOL;
    echo '─' . str_repeat('─', 60) . PHP_EOL;
    $totalVariants = 0;
    foreach ($user->store->products as $i => $p) {
        $variants = $p->variants ?? [];
        $variantCount = count($variants);
        $totalVariants += $variantCount;
        echo ($i+1) . '. ' . $p->name . PHP_EOL;
        echo '   Harga  : Rp ' . number_format($p->price, 0, ',', '.') . PHP_EOL;
        echo '   Stok   : ' . $p->stock . PHP_EOL;
        echo '   Varian : ' . $variantCount . ' varian' . PHP_EOL;
        foreach ($variants as $v) {
            echo '     • ' . $v['name'] . ': Rp ' . number_format($v['price'] ?? $p->price, 0, ',', '.') . PHP_EOL;
        }
        echo PHP_EOL;
    }
    echo '─' . str_repeat('─', 60) . PHP_EOL;
    echo '📊 SUMMARY' . PHP_EOL;
    echo '─' . str_repeat('─', 60) . PHP_EOL;
    echo 'Total Products : ' . $user->store->products->count() . PHP_EOL;
    echo 'Total Variants : ' . $totalVariants . PHP_EOL;
    echo 'Total Stock    : ' . $user->store->products->sum('stock') . ' items' . PHP_EOL;
    echo PHP_EOL . '🎉 All data successfully seeded!' . PHP_EOL;
} else {
    echo PHP_EOL . '❌ ERROR: Seeder failed - user Abiyu not found' . PHP_EOL;
}
"

echo.
echo [3/3] Complete!
echo.
pause
