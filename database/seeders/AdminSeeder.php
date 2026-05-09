<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['phone' => '08123456789'], [
            'name' => 'Admin Arradea',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_seller' => false,
            'phone_verified_at' => now(),
            'wilayah' => 'Jakarta',
            'latitude' => env('CENTER_LAT', -6.5723514245397086),
            'longitude' => env('CENTER_LNG', 106.77478524708685),
        ]);

        echo "✅ Admin account created:\n";
        echo "   Phone: 08123456789\n";
        echo "   Password: admin123\n";
        echo "   Role: Admin\n";
    }
}
