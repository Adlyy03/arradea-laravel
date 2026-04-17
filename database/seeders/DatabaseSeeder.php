<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\AccessCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SellerSeeder::class,
        ]);

        $activeAccessCode = AccessCode::where('is_active', true)->first()
            ?? AccessCode::create([
                'code' => 'ARRADEA-DEFAULT',
                'is_active' => true,
            ]);

        // 1. Create Admin (skip if exists)
        User::updateOrCreate(
            ['phone' => '081200009999'],
            [
                'name' => 'Admin Arradea',
                'wilayah' => 'Arradea',
                'access_code_id' => $activeAccessCode->id,
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone_verified_at' => now(),
            ]
        );

        // 2. Create Additional Buyers
        $buyers = [
            ['name' => 'Ahmad Rahman', 'phone' => '081300000001'],
            ['name' => 'Maya Sari', 'phone' => '081300000002'],
            ['name' => 'Dimas Putra', 'phone' => '081300000003'],
            ['name' => 'Rina Amelia', 'phone' => '081300000004'],
            ['name' => 'Fajar Nugroho', 'phone' => '081300000005'],
            ['name' => 'Lestari Dewi', 'phone' => '081300000006'],
            ['name' => 'Bayu Santoso', 'phone' => '081300000007'],
            ['name' => 'Nadia Putri', 'phone' => '081300000008'],
        ];

        foreach ($buyers as $buyerData) {
            User::updateOrCreate(
                ['phone' => $buyerData['phone']],
                [
                    'name' => $buyerData['name'],
                    'wilayah' => 'Arradea',
                    'access_code_id' => $activeAccessCode->id,
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone_verified_at' => now(),
                ]
            );
        }

        // 3. Create Sample Orders
        $this->createSampleOrders();
    }

    private function createSampleOrders()
    {
        $buyers = User::where('role', 'buyer')->get();
        $products = Product::with('store')->get();

        // Create some random orders
        for ($i = 0; $i < 25; $i++) {
            $buyer = $buyers->random();
            $product = $products->random();
            $quantity = rand(1, 3);
            $statuses = ['pending', 'accepted', 'done', 'rejected'];

            Order::create([
                'user_id' => $buyer->id,
                'store_id' => $product->store_id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
