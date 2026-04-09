<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        $admin = User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin Test',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Create seller
        $seller = User::firstOrCreate(['email' => 'seller@test.com'], [
            'name' => 'Seller Test',
            'password' => bcrypt('password'),
            'role' => 'seller'
        ]);

        // Create store for seller
        $store = Store::firstOrCreate(['user_id' => $seller->id], [
            'name' => 'Toko Test Seller',
            'description' => 'Toko untuk testing marketplace'
        ]);

        // Create products
        Product::firstOrCreate(['name' => 'Produk Test 1'], [
            'store_id' => $store->id,
            'price' => 50000,
            'stock' => 10,
            'description' => 'Produk test pertama untuk marketplace'
        ]);

        Product::firstOrCreate(['name' => 'Produk Test 2'], [
            'store_id' => $store->id,
            'price' => 75000,
            'stock' => 5,
            'description' => 'Produk test kedua untuk marketplace'
        ]);

        // Create buyer
        $buyer = User::firstOrCreate(['email' => 'buyer@test.com'], [
            'name' => 'Buyer Test',
            'password' => bcrypt('password'),
            'role' => 'buyer'
        ]);

        echo "Test users created:\n";
        echo "- Admin: admin@test.com / password\n";
        echo "- Seller: seller@test.com / password\n";
        echo "- Buyer: buyer@test.com / password\n";
    }
}
