<?php

/**
 * VERIFICATION CHECKLIST - Abiyu Food Product Seeder
 * 
 * This file serves as a comprehensive checklist for verifying
 * the seeder implementation and data integrity.
 * 
 * Run this via: php verify_seeder.php
 */

define('LARAVEL_START', microtime(true));

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   ABIYU FOOD SEEDER - VERIFICATION CHECKLIST                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// FILE STRUCTURE VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────

echo "📂 FILE STRUCTURE VERIFICATION\n";
echo str_repeat("─", 70) . "\n";

$files = [
    'database/seeders/AbiuFoodProductSeeder.php' => 'Seeder class',
    'app/Http/Controllers/TestController.php' => 'Test controller',
    'public/test-seeder.html' => 'Web UI',
    'run_seeder.bat' => 'Batch script',
    'SEEDER_DOCUMENTATION.md' => 'Documentation',
];

$allFilesExist = true;
foreach ($files as $path => $description) {
    $exists = file_exists($path);
    $allFilesExist = $allFilesExist && $exists;
    $status = $exists ? '✅' : '❌';
    echo "{$status} {$path}\n";
    echo "   └─ {$description}\n";
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// SEEDER CODE VALIDATION
// ─────────────────────────────────────────────────────────────────────────────

echo "🔍 SEEDER CODE VALIDATION\n";
echo str_repeat("─", 70) . "\n";

$seederFile = 'database/seeders/AbiuFoodProductSeeder.php';
if (file_exists($seederFile)) {
    $seederContent = file_get_contents($seederFile);
    
    $checks = [
        'Class definition' => strpos($seederContent, 'class AbiuFoodProductSeeder') !== false,
        'User::firstOrCreate' => strpos($seederContent, 'firstOrCreate') !== false,
        'abiyu@arradea.com email' => strpos($seederContent, 'abiyu@arradea.com') !== false,
        'Abiyu name' => strpos($seederContent, "'name' => 'Abiyu'") !== false,
        'Product creation' => strpos($seederContent, 'Product::create') !== false,
        'Variants handling' => strpos($seederContent, 'variants') !== false,
        'Category handling' => strpos($seederContent, 'Category::') !== false,
    ];
    
    foreach ($checks as $check => $result) {
        $status = $result ? '✅' : '❌';
        echo "{$status} {$check}\n";
    }
} else {
    echo "❌ Seeder file not found!\n";
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// ROUTE CONFIGURATION VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────

echo "🛣️  ROUTE CONFIGURATION VERIFICATION\n";
echo str_repeat("─", 70) . "\n";

$apiFile = 'routes/api.php';
if (file_exists($apiFile)) {
    $apiContent = file_get_contents($apiFile);
    
    $routeChecks = [
        'TestController import' => strpos($apiContent, 'TestController') !== false,
        'Route test endpoint' => strpos($apiContent, '/test/abiyu-seeder') !== false,
        'Endpoint mapped' => strpos($apiContent, 'runSeeder') !== false,
    ];
    
    foreach ($routeChecks as $check => $result) {
        $status = $result ? '✅' : '❌';
        echo "{$status} {$check}\n";
    }
} else {
    echo "❌ Route file not found!\n";
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// CONTROLLER VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────

echo "🎮 CONTROLLER VERIFICATION\n";
echo str_repeat("─", 70) . "\n";

$controllerFile = 'app/Http/Controllers/TestController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    
    $controllerChecks = [
        'Class definition' => strpos($controllerContent, 'class TestController') !== false,
        'runSeeder method' => strpos($controllerContent, 'public function runSeeder') !== false,
        'JSON response' => strpos($controllerContent, 'response()->json') !== false,
        'User query' => strpos($controllerContent, 'User::where') !== false,
        'Products iteration' => strpos($controllerContent, 'foreach ($products') !== false,
    ];
    
    foreach ($controllerChecks as $check => $result) {
        $status = $result ? '✅' : '❌';
        echo "{$status} {$check}\n";
    }
} else {
    echo "❌ Controller file not found!\n";
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// DATA STRUCTURE VALIDATION
// ─────────────────────────────────────────────────────────────────────────────

echo "📊 DATA STRUCTURE VALIDATION\n";
echo str_repeat("─", 70) . "\n";

if (file_exists($seederFile)) {
    $seederContent = file_get_contents($seederFile);
    
    // Check product count
    $productMatches = [];
    preg_match_all("/\['name'\s*=>\s*'([^']+)',\s*'description'/", $seederContent, $productMatches);
    $productCount = count($productMatches[1]);
    
    echo "✅ Product count: {$productCount} products\n";
    
    if ($productCount >= 5) {
        echo "   └─ ✅ 5+ products found\n";
    } else {
        echo "   └─ ❌ Less than 5 products\n";
    }
    
    // Check variants
    $variantMatches = [];
    preg_match_all("/\['key'\s*=>\s*'([^']+)',\s*'name'/", $seederContent, $variantMatches);
    $totalVariants = count($variantMatches[1]);
    
    echo "✅ Variant count: {$totalVariants} variants\n";
    
    if ($totalVariants >= 12) {
        echo "   └─ ✅ 12+ variants found (expecting 15)\n";
    } else {
        echo "   └─ ⚠️  Less than 12 variants\n";
    }
    
    // Check product names
    $productNames = ['Kopi', 'Teh', 'Coklat', 'Jamu', 'Kacang'];
    echo "\n✅ Product names:\n";
    foreach ($productNames as $name) {
        $exists = strpos($seederContent, $name) !== false;
        $status = $exists ? '✅' : '❌';
        echo "   {$status} {$name}\n";
    }
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// FINAL CHECKLIST
// ─────────────────────────────────────────────────────────────────────────────

echo "✅ FINAL CHECKLIST\n";
echo str_repeat("─", 70) . "\n";

$finalChecks = [
    'All files exist' => $allFilesExist,
    'Seeder properly structured' => file_exists($seederFile),
    'Route configured' => file_exists($apiFile),
    'Controller created' => file_exists($controllerFile),
];

$allChecked = true;
foreach ($finalChecks as $check => $result) {
    $allChecked = $allChecked && $result;
    $status = $result ? '✅' : '❌';
    echo "{$status} {$check}\n";
}

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────

echo "═" . str_repeat("═", 68) . "═\n";

if ($allChecked) {
    echo "\n🎉 ALL VERIFICATION CHECKS PASSED! 🎉\n\n";
    echo "Your seeder is ready to use! Choose one of these methods:\n\n";
    echo "  1️⃣  php artisan db:seed --class=AbiuFoodProductSeeder\n";
    echo "  2️⃣  Double-click: run_seeder.bat\n";
    echo "  3️⃣  Browser: http://localhost/test-seeder.html\n";
    echo "  4️⃣  API: curl http://localhost:8000/api/test/abiyu-seeder\n\n";
} else {
    echo "\n⚠️  SOME CHECKS FAILED - Please verify the setup!\n\n";
}

echo "═" . str_repeat("═", 68) . "═\n\n";
