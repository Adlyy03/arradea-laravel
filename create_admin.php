<?php
/**
 * Script untuk membuat akun admin dummy
 * Jalankan: php create_admin.php
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;

try {
    // Check if admin already exists
    $existingAdmin = User::where('phone', '08123456789')->first();
    
    if ($existingAdmin) {
        echo "⚠️  Admin sudah ada:\n";
        echo "   ID: {$existingAdmin->id}\n";
        echo "   Nama: {$existingAdmin->name}\n";
        echo "   Phone: {$existingAdmin->phone}\n";
        echo "   Role: {$existingAdmin->role}\n";
        exit;
    }

    // Create admin account with location from .env
    $admin = User::create([
        'name' => 'Admin Arradea',
        'phone' => '08123456789',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
        'is_seller' => false,
        'phone_verified_at' => now(),
        'wilayah' => 'Jakarta',
        'latitude' => env('CENTER_LAT', -6.5723514245397086),
        'longitude' => env('CENTER_LNG', 106.77478524708685),
    ]);

    echo "✅ Admin account berhasil dibuat!\n\n";
    echo "═══════════════════════════════════════════\n";
    echo "📱 LOGIN CREDENTIALS:\n";
    echo "═══════════════════════════════════════════\n";
    echo "Phone:    08123456789\n";
    echo "Password: admin123\n";
    echo "Role:     Admin\n";
    echo "═══════════════════════════════════════════\n";
    echo "\nUser ID: {$admin->id}\n";
    echo "Created at: {$admin->created_at}\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
