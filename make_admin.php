<?php
/**
 * Script untuk membuat/ubah akun menjadi admin
 * Jalankan: php make_admin.php [phone_number]
 * 
 * Contoh:
 * - php make_admin.php 0895321217645
 * - php make_admin.php 08123456789
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$phone = $argv[1] ?? '0895321217645';

try {
    // Check if user exists
    $user = User::where('phone', $phone)->first();
    
    if (!$user) {
        // Create new admin user
        $user = User::create([
            'name' => 'Admin Arradea',
            'phone' => $phone,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_seller' => false,
            'phone_verified_at' => now(),
            'wilayah' => 'Arradea',
            'latitude' => env('CENTER_LAT', -6.5723514245397086),
            'longitude' => env('CENTER_LNG', 106.77478524708685),
            'access_code_id' => 1,
        ]);
        
        echo "✅ Admin account berhasil dibuat!\n\n";
    } else {
        // Update existing user to admin
        $user->update([
            'role' => 'admin',
            'is_seller' => false,
            'phone_verified_at' => $user->phone_verified_at ?? now(),
        ]);
        
        echo "✅ Akun berhasil diubah menjadi admin!\n\n";
    }

    echo "═══════════════════════════════════════════════════════\n";
    echo "📱 ADMIN ACCOUNT DETAILS:\n";
    echo "═══════════════════════════════════════════════════════\n";
    echo "ID:           {$user->id}\n";
    echo "Name:         {$user->name}\n";
    echo "Phone:        {$user->phone}\n";
    echo "Role:         {$user->role}\n";
    echo "Is Seller:    " . ($user->is_seller ? 'Yes' : 'No') . "\n";
    echo "Location:     {$user->latitude}, {$user->longitude}\n";
    echo "Wilayah:      {$user->wilayah}\n";
    echo "Status:       ✅ Active (langsung bisa login)\n";
    echo "═══════════════════════════════════════════════════════\n";
    echo "\n🔑 LOGIN INFO:\n";
    echo "   Phone:    {$user->phone}\n";
    echo "   Password: admin123\n";
    echo "\n✨ Tidak perlu persetujuan admin lain!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
