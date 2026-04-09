<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $categories = \App\Models\Category::all()->keyBy('slug');

        $products = [
            // Elektronik Products
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'description' => 'iPhone terbaru dengan kamera canggih, performa tinggi, dan desain premium.',
                'price' => 18999000,
                'stock' => 5,
                'category_slug' => 'smartphone',
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'MacBook Air M3 13-inch',
                'description' => 'Laptop ultrabook dengan chip M3, baterai tahan lama, dan desain tipis.',
                'price' => 22999000,
                'stock' => 3,
                'category_slug' => 'laptop',
                'image' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Headphone wireless dengan noise cancelling terbaik dan kualitas suara premium.',
                'price' => 4999000,
                'stock' => 8,
                'category_slug' => 'audio',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'PlayStation 5 Console',
                'description' => 'Konsol game terbaru dengan grafis 4K dan loading super cepat.',
                'price' => 8999000,
                'stock' => 2,
                'category_slug' => 'gaming',
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Kamera mirrorless full-frame dengan 4K video dan autofocus canggih.',
                'price' => 45999000,
                'stock' => 1,
                'category_slug' => 'kamera',
                'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Fashion Products
            [
                'name' => 'Levi\'s 501 Original Jeans',
                'description' => 'Celana jeans klasik dengan bahan denim premium dan potongan straight fit.',
                'price' => 1299000,
                'stock' => 15,
                'category_slug' => 'pakaian-pria',
                'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Sepatu sneakers dengan teknologi Air Max untuk kenyamanan maksimal.',
                'price' => 1899000,
                'stock' => 12,
                'category_slug' => 'sepatu',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Gucci Marmont Matelassé Bag',
                'description' => 'Tas tangan mewah dengan desain GG monogram dan bahan kulit premium.',
                'price' => 15999000,
                'stock' => 2,
                'category_slug' => 'tas',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Rolex Submariner Date',
                'description' => 'Jam tangan klasik dengan movement automatic dan ketahanan air 300m.',
                'price' => 185000000,
                'stock' => 1,
                'category_slug' => 'aksesoris',
                'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Rumah Tangga Products
            [
                'name' => 'KitchenAid Stand Mixer',
                'description' => 'Mixer berdiri profesional dengan 10 kecepatan dan berbagai attachment.',
                'price' => 8999000,
                'stock' => 4,
                'category_slug' => 'dapur',
                'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Tempur-Pedic Mattress',
                'description' => 'Kasur memory foam premium untuk tidur nyenyak dan kesehatan tulang belakang.',
                'price' => 15999000,
                'stock' => 3,
                'category_slug' => 'kamar-tidur',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Ikea KIVIK Sofa',
                'description' => 'Sofa modular dengan desain modern dan bahan berkualitas tinggi.',
                'price' => 7999000,
                'stock' => 6,
                'category_slug' => 'ruang-tamu',
                'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Kesehatan & Kecantikan Products
            [
                'name' => 'The Ordinary Hyaluronic Acid',
                'description' => 'Serum pelembab dengan hyaluronic acid murni untuk kulit lembab dan kenyal.',
                'price' => 189000,
                'stock' => 25,
                'category_slug' => 'skincare',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Dior Addict Lip Glow',
                'description' => 'Lip tint dengan efek glow natural dan tahan lama.',
                'price' => 499000,
                'stock' => 18,
                'category_slug' => 'makeup',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Chanel No.5 Parfum',
                'description' => 'Parfum klasik dengan aroma floral aldehydic yang timeless.',
                'price' => 2999000,
                'stock' => 7,
                'category_slug' => 'parfum',
                'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Olahraga Products
            [
                'name' => 'Peloton Bike+',
                'description' => 'Sepeda statis cerdas dengan layar HD dan kelas olahraga interaktif.',
                'price' => 34999000,
                'stock' => 2,
                'category_slug' => 'fitness',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Nike Air Zoom Pegasus',
                'description' => 'Sepatu lari dengan teknologi Zoom Air untuk responsivitas maksimal.',
                'price' => 1499000,
                'stock' => 20,
                'category_slug' => 'sepatu',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Hobi & Koleksi Products
            [
                'name' => 'Fender American Ultra II Telecaster',
                'description' => 'Gitar elektrik premium dengan pickup V-Mod II dan neck Ultra Modern.',
                'price' => 45999000,
                'stock' => 1,
                'category_slug' => 'musik',
                'image' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Catan Board Game',
                'description' => 'Permainan strategi untuk 3-4 pemain dengan tema perdagangan dan kolonisasi.',
                'price' => 899000,
                'stock' => 10,
                'category_slug' => 'board-games',
                'image' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Otomotif Products
            [
                'name' => 'Michelin Pilot Sport 4S',
                'description' => 'Ban mobil premium untuk performa tinggi dan handling maksimal.',
                'price' => 2999000,
                'stock' => 8,
                'category_slug' => 'sparepart',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],

            // Buku Products
            [
                'name' => 'Atomic Habits by James Clear',
                'description' => 'Buku best-seller tentang perubahan kebiasaan kecil untuk hasil besar.',
                'price' => 99000,
                'stock' => 30,
                'category_slug' => 'novel',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
        ];

        // Assign products to sellers randomly
        foreach ($products as $index => $productData) {
            $seller = $sellers->get($index % $sellers->count());
            $category = $categories->get($productData['category_slug']);

            if ($seller && $category) {
                \App\Models\Product::create([
                    'store_id' => $seller->store->id,
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image' => $productData['image'],
                ]);
            }
        }

        // Create additional random products for more variety
        $additionalProducts = [
            ['name' => 'Samsung Galaxy S24 Ultra', 'category' => 'smartphone', 'price' => 15999000, 'stock' => 7],
            ['name' => 'Dell XPS 13 Laptop', 'category' => 'laptop', 'price' => 18999000, 'stock' => 4],
            ['name' => 'Adidas Ultraboost 22', 'category' => 'sepatu', 'price' => 2299000, 'stock' => 14],
            ['name' => 'Dyson V15 Vacuum', 'category' => 'rumah-tangga', 'price' => 8999000, 'stock' => 5],
            ['name' => 'La Mer Crème de la Mer', 'category' => 'skincare', 'price' => 4999000, 'stock' => 6],
            ['name' => 'Bowflex Xtreme 2 SE', 'category' => 'fitness', 'price' => 12999000, 'stock' => 3],
            ['name' => 'LEGO Creator 3-in-1', 'category' => 'hobi-koleksi', 'price' => 799000, 'stock' => 12],
            ['name' => 'Mobil 1 Synthetic Oil', 'category' => 'otomotif', 'price' => 299000, 'stock' => 20],
            ['name' => 'Harry Potter Series', 'category' => 'novel', 'price' => 499000, 'stock' => 8],
        ];

        foreach ($additionalProducts as $productData) {
            $seller = $sellers->random();
            $category = $categories->where('slug', $productData['category'])->first();

            if ($seller && $category) {
                \App\Models\Product::create([
                    'store_id' => $seller->store->id,
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'description' => 'Produk berkualitas dengan garansi resmi.',
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image' => 'https://via.placeholder.com/400x300?text=' . urlencode($productData['name']),
                ]);
            }
        }
    }
}
