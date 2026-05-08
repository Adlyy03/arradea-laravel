<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Preferred mode: 'buyer' or 'seller' (default buyer)
            // This is saved to DB so user's last choice is remembered
            $table->enum('preferred_mode', ['buyer', 'seller'])
                  ->default('buyer')
                  ->after('is_seller')
                  ->comment('User preferred mode for next login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('preferred_mode');
        });
    }
};
