<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Run the application
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STARTING ABIYU FOOD SEEDER ===\n\n";

// Run seeder
$seeder = new \Database\Seeders\AbiuFoodProductSeeder();
$seeder->run();

echo "✅ Seeder berhasil dijalankan!\n\n";

// Testing: Get Abiyu seller data
echo "=== TESTING DATA ===\n\n";

$user = \App\Models\User::where('email', 'abiyu@arradea.com')->first();

if ($user) {
    echo "✅ Seller Abiyu ditemukan:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Name: {$user->name}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - Role: {$user->role}\n\n";

    $store = $user->store;
    if ($store) {
        echo "✅ Store ditemukan:\n";
        echo "   - ID: {$store->id}\n";
        echo "   - Name: {$store->name}\n";
        echo "   - Description: {$store->description}\n";
        echo "   - Address: {$store->address}\n\n";

        $products = $store->products;
        echo "✅ Products (" . $products->count() . " produk):\n";
        echo str_repeat("─", 100) . "\n";

        foreach ($products as $index => $product) {
            echo "\n{$index}. {$product->name}\n";
            echo "   - Harga: Rp " . number_format($product->price, 0, ',', '.') . "\n";
            echo "   - Stok: {$product->stock}\n";
            echo "   - Kategori: {$product->category->name}\n";

            if ($product->variants && count($product->variants) > 0) {
                echo "   - Varian:\n";
                foreach ($product->variants as $variant) {
                    $variantPrice = $variant['price'] ?? $product->price;
                    $variantStock = $variant['stock'] ?? 0;
                    echo "      • {$variant['name']}: Rp " . number_format($variantPrice, 0, ',', '.') . " (Stok: {$variantStock})\n";
                }
            }
        }

        echo "\n" . str_repeat("─", 100) . "\n";
        echo "\n✅ TOTAL VARIAN:\n";
        $totalVariants = 0;
        foreach ($products as $product) {
            if ($product->variants) {
                $count = count($product->variants);
                $totalVariants += $count;
                echo "   - {$product->name}: {$count} varian\n";
            }
        }
        echo "   - TOTAL: {$totalVariants} varian\n\n";

        echo "✅ SUMMARY:\n";
        echo "   ✓ Seller Abiyu: Created\n";
        echo "   ✓ Store: {$store->name}\n";
        echo "   ✓ Produk: " . $products->count() . " produk\n";
        echo "   ✓ Varian: {$totalVariants} varian\n";
        echo "   ✓ Total Stok: " . $products->sum('stock') . " item\n\n";
        echo "🎉 TESTING COMPLETE - Semua data berhasil disimpan!\n";
    } else {
        echo "❌ Store tidak ditemukan untuk user Abiyu\n";
    }
} else {
    echo "❌ Seller Abiyu tidak ditemukan\n";
}

// Disconnect database
$app->terminating(function() {
    \Illuminate\Support\Facades\DB::disconnect();
});
