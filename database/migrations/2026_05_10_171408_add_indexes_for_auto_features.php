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
        Schema::table('products', function (Blueprint $table) {
            // Index untuk query produk dengan diskon aktif
            $table->index(['discount_start_at', 'discount_end_at'], 'idx_products_discount_period');
            $table->index('discount_percent', 'idx_products_discount_percent');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index untuk query seller dengan auto schedule
            if (!Schema::hasColumn('users', 'auto_schedule')) {
                return;
            }
            $table->index(['role', 'auto_schedule', 'store_status'], 'idx_users_seller_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_discount_period');
            $table->dropIndex('idx_products_discount_percent');
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'idx_users_seller_schedule')) {
                $table->dropIndex('idx_users_seller_schedule');
            }
        });
    }
};
