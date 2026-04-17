<?php

namespace Database\Seeders;

use App\Models\AccessCode;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $accessCode = AccessCode::firstOrCreate(
            ['code' => 'ARRADEA-SELLER'],
            ['is_active' => true]
        );

        $sellers = [
            [
                'name' => 'Toko Adli Arradea',
                'phone' => '081200000001',
                'store_name' => 'Toko Adli',
                'store_description' => 'Toko kebutuhan harian di wilayah Arradea.',
                'address' => 'Komplek Arradea Blok A1',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'products' => [
                    ['name' => 'Paket Sembako Arradea', 'price' => 125000, 'stock' => 20],
                    ['name' => 'Snack Box Arradea', 'price' => 45000, 'stock' => 35],
                ],
            ],
            [
                'name' => 'Toko Sari Arradea',
                'phone' => '081200000002',
                'store_name' => 'Toko Sari',
                'store_description' => 'Aneka perlengkapan rumah tangga dan dapur.',
                'address' => 'Komplek Arradea Blok B2',
                'latitude' => -6.199700,
                'longitude' => 106.816900,
                'products' => [
                    ['name' => 'Set Alat Makan', 'price' => 89000, 'stock' => 18],
                    ['name' => 'Piring Keramik', 'price' => 65000, 'stock' => 24],
                ],
            ],
            [
                'name' => 'Toko Maju Arradea',
                'phone' => '081200000003',
                'store_name' => 'Toko Maju',
                'store_description' => 'Produk elektronik kecil dan aksesoris gadget.',
                'address' => 'Komplek Arradea Blok C3',
                'latitude' => -6.200250,
                'longitude' => 106.816350,
                'products' => [
                    ['name' => 'Charger Fast Charging', 'price' => 99000, 'stock' => 40],
                    ['name' => 'Kabel Data Type-C', 'price' => 35000, 'stock' => 50],
                ],
            ],
            [
                'name' => 'Toko Cantik Arradea',
                'phone' => '081200000004',
                'store_name' => 'Toko Cantik',
                'store_description' => 'Produk fashion dan aksesoris wanita.',
                'address' => 'Komplek Arradea Blok D4',
                'latitude' => -6.199450,
                'longitude' => 106.817050,
                'products' => [
                    ['name' => 'Tas Selempang', 'price' => 135000, 'stock' => 16],
                    ['name' => 'Hijab Premium', 'price' => 75000, 'stock' => 30],
                ],
            ],
            [
                'name' => 'Toko Segar Arradea',
                'phone' => '081200000005',
                'store_name' => 'Toko Segar',
                'store_description' => 'Minuman, cemilan, dan kebutuhan harian cepat saji.',
                'address' => 'Komplek Arradea Blok E5',
                'latitude' => -6.200550,
                'longitude' => 106.816150,
                'products' => [
                    ['name' => 'Kopi Susu Botol', 'price' => 22000, 'stock' => 60],
                    ['name' => 'Roti Manis', 'price' => 18000, 'stock' => 45],
                ],
            ],
        ];

        foreach ($sellers as $sellerData) {
            $seller = User::updateOrCreate(
                ['phone' => $sellerData['phone']],
                [
                    'name' => $sellerData['name'],
                    'wilayah' => 'Arradea',
                    'latitude' => $sellerData['latitude'],
                    'longitude' => $sellerData['longitude'],
                    'access_code_id' => $accessCode->id,
                    'password' => Hash::make('password'),
                    'phone_verified_at' => now(),
                    'is_seller' => true,
                    'role' => 'seller',
                    'seller_status' => 'approved',
                    'seller_applied_at' => now(),
                    'seller_approved_at' => now(),
                    'seller_rejected_at' => null,
                    'seller_rejection_reason' => null,
                    'seller_otp_verified' => false,
                    'store_status' => 'open',
                ]
            );

            $store = Store::updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'name' => $sellerData['store_name'],
                    'description' => $sellerData['store_description'],
                    'address' => $sellerData['address'],
                    'status' => 'active',
                    'approved_at' => now(),
                ]
            );

            foreach ($sellerData['products'] as $productData) {
                Product::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name' => $productData['name'],
                    ],
                    [
                        'description' => $productData['name'] . ' tersedia untuk warga Arradea.',
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                        'image' => 'https://via.placeholder.com/600x400?text=' . urlencode($productData['name']),
                    ]
                );
            }
        }
    }
}
