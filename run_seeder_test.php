<?php
define('LARAVEL_START', microtime(true));

// Register the auto loader
require __DIR__ . '/vendor/autoload.php';

// Create application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Make sure we're using the right database
\Illuminate\Support\Facades\Config::set('database.default', env('DB_CONNECTION', 'sqlite'));

try {
    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║           RUNNING ABIYU FOOD PRODUCT SEEDER                    ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";

    // Run the seeder
    $seeder = new \Database\Seeders\AbiuFoodProductSeeder();
    echo "[1/2] Running seeder...\n";
    $seeder->run();
    echo "✓ Seeder executed successfully!\n\n";

    // Testing
    echo "[2/2] Testing data...\n";
    echo str_repeat("─", 70) . "\n";

    $user = \App\Models\User::where('email', 'abiyu@arradea.com')->with('store.products.category')->first();

    if ($user) {
        echo "\n✅ SELLER INFORMATION\n";
        echo str_repeat("─", 70) . "\n";
        echo "ID           : {$user->id}\n";
        echo "Name         : {$user->name}\n";
        echo "Email        : {$user->email}\n";
        echo "Role         : {$user->role}\n";

        if ($user->store) {
            $store = $user->store;
            echo "\n✅ STORE INFORMATION\n";
            echo str_repeat("─", 70) . "\n";
            echo "Store ID     : {$store->id}\n";
            echo "Store Name   : {$store->name}\n";
            echo "Description  : {$store->description}\n";
            echo "Address      : {$store->address}\n";

            $products = $store->products;
            echo "\n✅ PRODUCTS & VARIANTS (" . $products->count() . " Products)\n";
            echo str_repeat("─", 70) . "\n";

            $totalVariants = 0;
            $totalStock = 0;

            foreach ($products as $idx => $product) {
                echo "\n[{$idx}] {$product->name}\n";
                echo "    Price       : Rp " . number_format($product->price, 0, ',', '.') . "\n";
                echo "    Stock       : {$product->stock}\n";
                echo "    Category    : {$product->category->name}\n";

                $variants = $product->variants ?? [];
                echo "    Variants    : " . count($variants) . " varian\n";

                if (count($variants) > 0) {
                    foreach ($variants as $var) {
                        $varPrice = $var['price'] ?? $product->price;
                        $varStock = $var['stock'] ?? 0;
                        echo "      ├─ {$var['name']}: Rp " . number_format($varPrice, 0, ',', '.') . " (Stock: {$varStock})\n";
                        $totalVariants++;
                        $totalStock += $varStock;
                    }
                }
            }

            echo "\n" . str_repeat("═", 70) . "\n";
            echo "✅ SUMMARY\n";
            echo str_repeat("─", 70) . "\n";
            echo "Total Products      : " . $products->count() . "\n";
            echo "Total Variants      : {$totalVariants}\n";
            echo "Total Stock (Var)   : {$totalStock}\n";
            echo "Base Product Stock  : " . $products->sum('stock') . "\n";
            echo "\n🎉 ALL DATA SUCCESSFULLY SEEDED AND TESTED!\n";
            echo str_repeat("═", 70) . "\n\n";
        } else {
            echo "\n❌ ERROR: Store not found for user Abiyu\n";
        }
    } else {
        echo "\n❌ ERROR: User Abiyu not found\n";
    }
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// Cleanup
$kernel->terminate(null, 0);
?>
