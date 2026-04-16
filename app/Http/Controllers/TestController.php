<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Database\Seeders\AbiuFoodProductSeeder;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Run seeder and return test results
     */
    public function runSeeder()
    {
        try {
            // Run seeder
            $seeder = new AbiuFoodProductSeeder();
            $seeder->run();

            // Get Abiyu user with relationships
            $user = User::where('email', 'abiyu@arradea.com')
                ->with('store.products.category')
                ->first();

            if (!$user || !$user->store) {
                return response()->json([
                    'success' => false,
                    'error' => 'Seeder executed but Abiyu user not found'
                ], 400);
            }

            $products = $user->store->products;
            $totalVariants = 0;
            $productsList = [];

            foreach ($products as $product) {
                $variants = $product->variants ?? [];
                $totalVariants += count($variants);

                $variantsList = [];
                foreach ($variants as $variant) {
                    $variantsList[] = [
                        'key' => $variant['key'] ?? 'unknown',
                        'name' => $variant['name'] ?? '',
                        'price' => $variant['price'] ?? $product->price,
                        'stock' => $variant['stock'] ?? 0,
                    ];
                }

                $productsList[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category' => $product->category->name,
                    'variants' => $variantsList,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Seeder executed successfully and data verified',
                'seller' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'store' => [
                    'id' => $user->store->id,
                    'name' => $user->store->name,
                    'description' => $user->store->description,
                    'address' => $user->store->address,
                ],
                'products' => $productsList,
                'summary' => [
                    'total_products' => $products->count(),
                    'total_variants' => $totalVariants,
                    'total_base_stock' => $products->sum('stock'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
