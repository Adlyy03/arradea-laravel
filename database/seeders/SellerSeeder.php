<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = [
            [
                'name' => 'Toko Elektronik Jakarta',
                'email' => 'seller1@arradea.com',
                'store_name' => 'Jakarta Gadget Store',
                'store_description' => 'Toko elektronik terpercaya di Jakarta dengan produk original dan bergaransi.',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat'
            ],
            [
                'name' => 'Fashion Boutique Bandung',
                'email' => 'seller2@arradea.com',
                'store_name' => 'Bandung Fashion House',
                'store_description' => 'Butik fashion dengan koleksi pakaian dan aksesoris terkini dari designer lokal.',
                'address' => 'Jl. Braga No. 45, Bandung'
            ],
            [
                'name' => 'Rumah Tangga Surabaya',
                'email' => 'seller3@arradea.com',
                'store_name' => 'Surabaya Home Store',
                'store_description' => 'Toko perlengkapan rumah tangga lengkap dengan harga terjangkau.',
                'address' => 'Jl. Tunjungan No. 78, Surabaya'
            ],
            [
                'name' => 'Kesehatan & Kecantikan Medan',
                'email' => 'seller4@arradea.com',
                'store_name' => 'Medan Beauty Center',
                'store_description' => 'Pusat kecantikan dan kesehatan dengan produk-produk berkualitas tinggi.',
                'address' => 'Jl. Ahmad Yani No. 56, Medan'
            ],
            [
                'name' => 'Olahraga Semarang',
                'email' => 'seller5@arradea.com',
                'store_name' => 'Semarang Sport Center',
                'store_description' => 'Toko olahraga lengkap dengan peralatan fitness dan outdoor.',
                'address' => 'Jl. Pandanaran No. 89, Semarang'
            ],
            [
                'name' => 'Hobi & Koleksi Yogyakarta',
                'email' => 'seller6@arradea.com',
                'store_name' => 'Yogyakarta Hobby Shop',
                'store_description' => 'Toko hobi dan koleksi unik untuk pecinta barang antik dan kreatif.',
                'address' => 'Jl. Malioboro No. 34, Yogyakarta'
            ],
            [
                'name' => 'Otomotif Makassar',
                'email' => 'seller7@arradea.com',
                'store_name' => 'Makassar Auto Parts',
                'store_description' => 'Spesialis sparepart dan aksesoris kendaraan bermotor.',
                'address' => 'Jl. Pettarani No. 67, Makassar'
            ],
            [
                'name' => 'Buku & Alat Tulis Bali',
                'email' => 'seller8@arradea.com',
                'store_name' => 'Bali Book Store',
                'store_description' => 'Toko buku dan alat tulis dengan koleksi lengkap untuk semua usia.',
                'address' => 'Jl. Legian No. 12, Denpasar, Bali'
            ],
            [
                'name' => 'Fashion Muslim Palembang',
                'email' => 'seller9@arradea.com',
                'store_name' => 'Palembang Muslim Fashion',
                'store_description' => 'Busana muslim modern dengan desain elegan dan nyaman.',
                'address' => 'Jl. Sudirman No. 90, Palembang'
            ],
            [
                'name' => 'Elektronik Murah Lampung',
                'email' => 'seller10@arradea.com',
                'store_name' => 'Lampung Electronic Mart',
                'store_description' => 'Elektronik murah berkualitas dengan harga terjangkau untuk semua kalangan.',
                'address' => 'Jl. Teuku Umar No. 45, Bandar Lampung'
            ],
            [
                'name' => 'Dekorasi Rumah Solo',
                'email' => 'seller11@arradea.com',
                'store_name' => 'Solo Home Decor',
                'store_description' => 'Dekorasi rumah dan interior design dengan gaya modern dan klasik.',
                'address' => 'Jl. Slamet Riyadi No. 78, Solo'
            ],
            [
                'name' => 'Suplemen Kesehatan Malang',
                'email' => 'seller12@arradea.com',
                'store_name' => 'Malang Health Store',
                'store_description' => 'Suplemen kesehatan dan vitamin alami untuk hidup sehat.',
                'address' => 'Jl. Ijen No. 23, Malang'
            ],
        ];

        foreach ($sellers as $sellerData) {
            $seller = \App\Models\User::create([
                'name' => $sellerData['name'],
                'email' => $sellerData['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'seller',
            ]);

            $seller->store()->create([
                'name' => $sellerData['store_name'],
                'description' => $sellerData['store_description'],
                'address' => $sellerData['address'],
            ]);
        }
    }
}
