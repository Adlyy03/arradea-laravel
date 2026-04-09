<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Parent categories
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Produk elektronik dan gadget terbaru', 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Pakaian, sepatu, dan aksesoris fashion', 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'description' => 'Produk untuk kebutuhan rumah dan dapur', 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'Kesehatan & Kecantikan', 'slug' => 'kesehatan-kecantikan', 'description' => 'Produk kesehatan, perawatan tubuh, dan kosmetik', 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'description' => 'Peralatan olahraga dan fitness', 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Hobi & Koleksi', 'slug' => 'hobi-koleksi', 'description' => 'Barang hobi, koleksi, dan entertainment', 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Otomotif', 'slug' => 'otomotif', 'description' => 'Aksesoris dan sparepart kendaraan', 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis', 'description' => 'Buku, novel, dan alat tulis menulis', 'is_featured' => false, 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // Subcategories
        $elektronik = \App\Models\Category::where('slug', 'elektronik')->first();
        $fashion = \App\Models\Category::where('slug', 'fashion')->first();
        $rumahTangga = \App\Models\Category::where('slug', 'rumah-tangga')->first();
        $kesehatan = \App\Models\Category::where('slug', 'kesehatan-kecantikan')->first();
        $olahraga = \App\Models\Category::where('slug', 'olahraga')->first();
        $hobi = \App\Models\Category::where('slug', 'hobi-koleksi')->first();
        $otomotif = \App\Models\Category::where('slug', 'otomotif')->first();
        $buku = \App\Models\Category::where('slug', 'buku-alat-tulis')->first();

        $subcategories = [
            // Elektronik subcategories
            ['name' => 'Smartphone', 'slug' => 'smartphone', 'parent_id' => $elektronik->id, 'description' => 'Handphone dan smartphone berbagai merek'],
            ['name' => 'Laptop', 'slug' => 'laptop', 'parent_id' => $elektronik->id, 'description' => 'Komputer laptop dan notebook'],
            ['name' => 'Audio', 'slug' => 'audio', 'parent_id' => $elektronik->id, 'description' => 'Headphone, speaker, dan audio device'],
            ['name' => 'Gaming', 'slug' => 'gaming', 'parent_id' => $elektronik->id, 'description' => 'Konsol game dan aksesoris gaming'],
            ['name' => 'Kamera', 'slug' => 'kamera', 'parent_id' => $elektronik->id, 'description' => 'Kamera digital dan aksesoris fotografi'],
            ['name' => 'TV & Monitor', 'slug' => 'tv-monitor', 'parent_id' => $elektronik->id, 'description' => 'Televisi dan monitor komputer'],

            // Fashion subcategories
            ['name' => 'Pakaian Pria', 'slug' => 'pakaian-pria', 'parent_id' => $fashion->id, 'description' => 'Baju, celana, dan pakaian pria'],
            ['name' => 'Pakaian Wanita', 'slug' => 'pakaian-wanita', 'parent_id' => $fashion->id, 'description' => 'Baju, rok, dan pakaian wanita'],
            ['name' => 'Sepatu', 'slug' => 'sepatu', 'parent_id' => $fashion->id, 'description' => 'Sepatu dan sandal fashion'],
            ['name' => 'Tas', 'slug' => 'tas', 'parent_id' => $fashion->id, 'description' => 'Tas tangan, ransel, dan dompet'],
            ['name' => 'Aksesoris', 'slug' => 'aksesoris', 'parent_id' => $fashion->id, 'description' => 'Jam, gelang, dan aksesoris fashion'],
            ['name' => 'Pakaian Anak', 'slug' => 'pakaian-anak', 'parent_id' => $fashion->id, 'description' => 'Pakaian untuk anak-anak'],

            // Rumah Tangga subcategories
            ['name' => 'Dapur', 'slug' => 'dapur', 'parent_id' => $rumahTangga->id, 'description' => 'Peralatan dapur dan masak'],
            ['name' => 'Kamar Tidur', 'slug' => 'kamar-tidur', 'parent_id' => $rumahTangga->id, 'description' => 'Sprei, bantal, dan furniture kamar'],
            ['name' => 'Ruang Tamu', 'slug' => 'ruang-tamu', 'parent_id' => $rumahTangga->id, 'description' => 'Sofa, meja, dan dekorasi ruang tamu'],
            ['name' => 'Kamar Mandi', 'slug' => 'kamar-mandi', 'parent_id' => $rumahTangga->id, 'description' => 'Peralatan kamar mandi dan cleaning'],
            ['name' => 'Dekorasi', 'slug' => 'dekorasi', 'parent_id' => $rumahTangga->id, 'description' => 'Hiasan rumah dan dekorasi interior'],

            // Kesehatan & Kecantikan subcategories
            ['name' => 'Skincare', 'slug' => 'skincare', 'parent_id' => $kesehatan->id, 'description' => 'Produk perawatan kulit wajah'],
            ['name' => 'Makeup', 'slug' => 'makeup', 'parent_id' => $kesehatan->id, 'description' => 'Kosmetik dan makeup'],
            ['name' => 'Parfum', 'slug' => 'parfum', 'parent_id' => $kesehatan->id, 'description' => 'Parfum dan wewangian'],
            ['name' => 'Suplemen', 'slug' => 'suplemen', 'parent_id' => $kesehatan->id, 'description' => 'Vitamin dan suplemen kesehatan'],
            ['name' => 'Alat Kesehatan', 'slug' => 'alat-kesehatan', 'parent_id' => $kesehatan->id, 'description' => 'Termometer, tensimeter, dll'],

            // Olahraga subcategories
            ['name' => 'Sepeda', 'slug' => 'sepeda', 'parent_id' => $olahraga->id, 'description' => 'Sepeda dan aksesoris bersepeda'],
            ['name' => 'Fitness', 'slug' => 'fitness', 'parent_id' => $olahraga->id, 'description' => 'Alat fitness dan gym equipment'],
            ['name' => 'Olahraga Tim', 'slug' => 'olahraga-tim', 'parent_id' => $olahraga->id, 'description' => 'Bola, net, dan perlengkapan olahraga tim'],
            ['name' => 'Renang', 'slug' => 'renang', 'parent_id' => $olahraga->id, 'description' => 'Peralatan renang dan kolam'],

            // Hobi & Koleksi subcategories
            ['name' => 'Musik', 'slug' => 'musik', 'parent_id' => $hobi->id, 'description' => 'Alat musik dan aksesoris'],
            ['name' => 'Fotografi', 'slug' => 'fotografi', 'parent_id' => $hobi->id, 'description' => 'Kamera dan perlengkapan fotografi'],
            ['name' => 'Board Games', 'slug' => 'board-games', 'parent_id' => $hobi->id, 'description' => 'Permainan papan dan kartu'],
            ['name' => 'Craft & DIY', 'slug' => 'craft-diy', 'parent_id' => $hobi->id, 'description' => 'Peralatan kerajinan tangan'],

            // Otomotif subcategories
            ['name' => 'Aksesoris Mobil', 'slug' => 'aksesoris-mobil', 'parent_id' => $otomotif->id, 'description' => 'Aksesoris interior dan eksterior mobil'],
            ['name' => 'Sparepart', 'slug' => 'sparepart', 'parent_id' => $otomotif->id, 'description' => 'Sparepart mobil dan motor'],
            ['name' => 'Oli & Pelumas', 'slug' => 'oli-pelumas', 'parent_id' => $otomotif->id, 'description' => 'Oli mesin dan pelumas kendaraan'],

            // Buku & Alat Tulis subcategories
            ['name' => 'Novel', 'slug' => 'novel', 'parent_id' => $buku->id, 'description' => 'Buku novel dan fiksi'],
            ['name' => 'Buku Pelajaran', 'slug' => 'buku-pelajaran', 'parent_id' => $buku->id, 'description' => 'Buku sekolah dan akademik'],
            ['name' => 'Komik', 'slug' => 'komik', 'parent_id' => $buku->id, 'description' => 'Komik dan manga'],
            ['name' => 'Alat Tulis', 'slug' => 'alat-tulis', 'parent_id' => $buku->id, 'description' => 'Pulpen, pensil, dan stationery'],
        ];

        foreach ($subcategories as $subcategory) {
            \App\Models\Category::create($subcategory);
        }
    }
}
