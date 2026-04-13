<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_seller')) {
                $table->boolean('is_seller')->default(false)->after('password');
            }
        });

        // Migrate legacy seller data into the new flexible flag.
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')
                ->where('role', 'seller')
                ->update(['is_seller' => true]);
        }

        if (Schema::hasColumn('users', 'seller_status')) {
            DB::table('users')
                ->where('seller_status', 'approved')
                ->update(['is_seller' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_seller')) {
                $table->dropColumn('is_seller');
            }
        });
    }
};
