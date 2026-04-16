<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;

class AbiuFoodProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create seller user (Abiyu)
        $seller = User::firstOrCreate(
            ['email' => 'abiyu@arradea.com'],
            [
                'name' => 'Abiyu',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'seller',
                'is_seller' => true,
            ]
        );

        // Create or get store
        $store = $seller->store ?? $seller->store()->create([
            'name' => 'Abiyu Food Store',
            'description' => 'Toko makanan berkualitas dengan produk pilihan terbaik dan varian rasa yang lezat.',
            'address' => 'Jl. Ahmad Yani No. 100, Jakarta',
            'status' => 'active',
        ]);

        // Get or create food parent category
        $foodParentCategory = Category::where('slug', 'makanan')->first();
        if (!$foodParentCategory) {
            $foodParentCategory = Category::create([
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan',
                'description' => 'Produk makanan dan minuman berkualitas',
                'is_featured' => true,
                'sort_order' => 9,
            ]);
        }

        // Get food category for products (use parent if no subcategory exists)
        $foodCategory = $foodParentCategory;

        // Food products with variants
        $products = [
            [
                'name' => 'Kopi Premium Arabika',
                'description' => 'Kopi arabika pilihan dari perkebunan terbaik Indonesia dengan cita rasa yang kaya dan aroma yang harum.',
                'price' => 89000,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b3f4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'variants' => [
                    [
                        'key' => 'medium-roast',
                        'name' => 'Medium Roast',
                        'price' => 89000,
                        'stock' => 25,
                    ],
                    [
                        'key' => 'dark-roast',
                        'name' => 'Dark Roast',
                        'price' => 95000,
                        'stock' => 20,
                    ],
                    [
                        'key' => 'light-roast',
                        'name' => 'Light Roast',
                        'price' => 85000,
                        'stock' => 5,
                    ],
                ],
            ],
            [
                'name' => 'Teh Hijau Organik',
                'description' => 'Teh hijau organik asli Indonesia tanpa bahan kimia sintetis dengan manfaat kesehatan maksimal.',
                'price' => 55000,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1597318372455-47d50847ac8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'variants' => [
                    [
                        'key' => 'loose-leaf',
                        'name' => 'Loose Leaf',
                        'price' => 55000,
                        'stock' => 20,
                    ],
                    [
                        'key' => 'tea-bag',
                        'name' => 'Tea Bag',
                        'price' => 65000,
                        'stock' => 15,
                    ],
                    [
                        'key' => 'powder',
                        'name' => 'Powder Mix',
                        'price' => 75000,
                        'stock' => 5,
                    ],
                ],
            ],
            [
                'name' => 'Coklat Premium Homemade',
                'description' => 'Coklat buatan tangan dengan bahan-bahan pilihan berkualitas tinggi dan rasa yang luar biasa enak.',
                'price' => 125000,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1578869645900-bacd7c555ea1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'variants' => [
                    [
                        'key' => 'dark-chocolate',
                        'name' => 'Dark Chocolate 70%',
                        'price' => 125000,
                        'stock' => 12,
                    ],
                    [
                        'key' => 'milk-chocolate',
                        'name' => 'Milk Chocolate',
                        'price' => 115000,
                        'stock' => 10,
                    ],
                    [
                        'key' => 'white-chocolate',
                        'name' => 'White Chocolate',
                        'price' => 110000,
                        'stock' => 8,
                    ],
                ],
            ],
            [
                'name' => 'Jamu Tradisional Asli',
                'description' => 'Jamu tradisional resep turun temurun dengan bahan-bahan alami pilihan yang menyehatkan tubuh.',
                'price' => 35000,
                'stock' => 60,
                'image' => 'https://images.unsplash.com/photo-1599599810694-b5ac4dd64b73?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'variants' => [
                    [
                        'key' => 'kunyit-asam',
                        'name' => 'Kunyit Asam',
                        'price' => 35000,
                        'stock' => 20,
                    ],
                    [
                        'key' => 'beras-kencur',
                        'name' => 'Beras Kencur',
                        'price' => 35000,
                        'stock' => 20,
                    ],
                    [
                        'key' => 'temulawak',
                        'name' => 'Temulawak',
                        'price' => 40000,
                        'stock' => 20,
                    ],
                ],
            ],
            [
                'name' => 'Kacang Panggang Premium',
                'description' => 'Kacang panggang dengan bumbu pilihan yang gurih, renyah dan sehat tanpa pengawet.',
                'price' => 45000,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1585329614529-e3ec48ab7b53?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'variants' => [
                    [
                        'key' => 'cashew',
                        'name' => 'Cashew Manis',
                        'price' => 55000,
                        'stock' => 15,
                    ],
                    [
                        'key' => 'almond',
                        'name' => 'Almond Pedas',
                        'price' => 50000,
                        'stock' => 18,
                    ],
                    [
                        'key' => 'mixed-nuts',
                        'name' => 'Mixed Nuts',
                        'price' => 45000,
                        'stock' => 17,
                    ],
                ],
            ],
        ];

        // Create products
        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);

            Product::create([
                'store_id' => $store->id,
                'category_id' => $foodCategory->id,
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'image' => $productData['image'],
                'variants' => $variants,
            ]);
        }
    }
}
