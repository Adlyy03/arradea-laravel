<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Categories, Sellers, and Products are already seeded
        // $this->call([
        //     CategorySeeder::class,
        //     SellerSeeder::class,
        //     ProductSeeder::class,
        // ]);

        // 1. Create Admin (skip if exists)
        User::firstOrCreate(
            ['email' => 'admin@arradea.com'],
            [
                'name' => 'Admin Arradea',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Create Additional Buyers
        $buyers = [
            ['name' => 'Ahmad Rahman', 'email' => 'ahmad@arradea.com'],
            ['name' => 'Maya Sari', 'email' => 'maya@arradea.com'],
            ['name' => 'Dimas Putra', 'email' => 'dimas@arradea.com'],
            ['name' => 'Rina Amelia', 'email' => 'rina@arradea.com'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@arradea.com'],
            ['name' => 'Lestari Dewi', 'email' => 'lestari@arradea.com'],
            ['name' => 'Bayu Santoso', 'email' => 'bayu@arradea.com'],
            ['name' => 'Nadia Putri', 'email' => 'nadia@arradea.com'],
        ];

        foreach ($buyers as $buyerData) {
            User::firstOrCreate(
                ['email' => $buyerData['email']],
                [
                    'name' => $buyerData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
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
