<?php

namespace Database\Seeders;

use App\Models\AccessCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClaudlySalesSeeder extends Seeder
{
    public function run(): void
    {
        $accessCode = AccessCode::where('is_active', true)->first()
            ?? AccessCode::firstOrCreate([
                'code' => 'ARRADEA-CLAUDLY',
            ], [
                'is_active' => true,
            ]);

        $seller = User::updateOrCreate(
            ['phone' => '081212340999'],
            [
                'name' => 'Claudly Fashion',
                'wilayah' => 'Arradea',
                'latitude' => -6.199880,
                'longitude' => 106.816720,
                'access_code_id' => $accessCode->id,
                'password' => Hash::make('password'),
                'phone_verified_at' => now(),
                'is_seller' => true,
                'role' => 'seller',
                'seller_status' => 'approved',
                'seller_applied_at' => now()->subMonths(6),
                'seller_approved_at' => now()->subMonths(6),
                'store_status' => 'open',
            ]
        );

        $store = Store::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'name' => 'Tokoo Baju Claudly',
                'description' => 'Toko fashion harian dan outfit trend untuk semua usia.',
                'address' => 'Komplek Arradea Blok F7',
                'status' => 'active',
                'approved_at' => now()->subMonths(6),
            ]
        );

        $products = [
            ['name' => 'Blouse Linen Wanita', 'price' => 129000, 'stock' => 220],
            ['name' => 'Kemeja Flanel Pria', 'price' => 149000, 'stock' => 180],
            ['name' => 'Celana Kulot Premium', 'price' => 139000, 'stock' => 190],
            ['name' => 'Kaos Oversize Basic', 'price' => 99000, 'stock' => 280],
            ['name' => 'Cardigan Rajut Casual', 'price' => 159000, 'stock' => 160],
            ['name' => 'Dress Midi Floral', 'price' => 189000, 'stock' => 140],
        ];

        $productModels = collect();

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                [
                    'store_id' => $store->id,
                    'name' => $productData['name'],
                ],
                [
                    'description' => $productData['name'] . ' kualitas premium dengan bahan nyaman dipakai harian.',
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image' => 'https://via.placeholder.com/600x400?text=' . urlencode($productData['name']),
                ]
            );

            $productModels->push($product);
        }

        $buyers = User::where('role', 'buyer')->get();
        if ($buyers->isEmpty()) {
            $buyers = $this->createFallbackBuyers($accessCode->id);
        }

        // Tambah riwayat pesanan yang cukup banyak dan tersebar 6 bulan terakhir.
        $targetOrderCount = 220;
        $statusPool = [
            'done', 'done', 'done', 'done', 'done',
            'accepted', 'accepted',
            'pending',
            'rejected',
            'dibatalkan',
        ];

        for ($i = 0; $i < $targetOrderCount; $i++) {
            $buyer = $buyers->random();
            $product = $productModels->random();
            $quantity = random_int(1, 4);

            $unitOriginal = (float) $product->price;
            $discountPercent = [0, 0, 5, 10, 15][array_rand([0, 1, 2, 3, 4])];
            $unitFinal = $unitOriginal * (1 - ($discountPercent / 100));
            $total = $unitFinal * $quantity;

            $createdAt = now()->subDays(random_int(0, 180))->setTime(random_int(8, 22), random_int(0, 59));
            $status = $statusPool[array_rand($statusPool)];

            Order::create([
                'user_id' => $buyer->id,
                'store_id' => $store->id,
                'product_id' => $product->id,
                'variant_key' => 'default',
                'quantity' => $quantity,
                'unit_price_original' => $unitOriginal,
                'unit_price_final' => $unitFinal,
                'discount_percent_applied' => $discountPercent,
                'total_price' => $total,
                'notes' => 'Dummy order Claudly #' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function createFallbackBuyers(int $accessCodeId)
    {
        $fallbackBuyers = collect([
            ['name' => 'Nadia Putri', 'phone' => '081377770001'],
            ['name' => 'Raka Pratama', 'phone' => '081377770002'],
            ['name' => 'Salsa Amalia', 'phone' => '081377770003'],
            ['name' => 'Fikri Hidayat', 'phone' => '081377770004'],
            ['name' => 'Dewi Lestari', 'phone' => '081377770005'],
        ]);

        return $fallbackBuyers->map(function (array $buyerData) use ($accessCodeId) {
            return User::updateOrCreate(
                ['phone' => $buyerData['phone']],
                [
                    'name' => $buyerData['name'],
                    'wilayah' => 'Arradea',
                    'access_code_id' => $accessCodeId,
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone_verified_at' => now(),
                ]
            );
        });
    }
}
