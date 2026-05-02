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
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('store_id', 'idx_products_store_id');
            $table->index('category_id', 'idx_products_category_id');
            $table->index('stock', 'idx_products_stock');
            $table->index('created_at', 'idx_products_created_at');
            $table->index(['store_id', 'stock'], 'idx_products_store_stock');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'idx_orders_user_id');
            $table->index('store_id', 'idx_orders_store_id');
            $table->index('product_id', 'idx_orders_product_id');
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created_at');
            $table->index(['store_id', 'status'], 'idx_orders_store_status');
            $table->index(['user_id', 'status'], 'idx_orders_user_status');
        });

        // Stores table indexes
        Schema::table('stores', function (Blueprint $table) {
            $table->index('user_id', 'idx_stores_user_id');
            $table->index('status', 'idx_stores_status');
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('parent_id', 'idx_categories_parent_id');
            $table->index('slug', 'idx_categories_slug');
            $table->index('is_featured', 'idx_categories_featured');
            $table->index('sort_order', 'idx_categories_sort_order');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone', 'idx_users_phone');
            $table->index('is_seller', 'idx_users_is_seller');
            $table->index('role', 'idx_users_role');
            $table->index('seller_status', 'idx_users_seller_status');
        });

        // Carts table indexes
        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id', 'idx_carts_user_id');
            $table->index('product_id', 'idx_carts_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_store_id');
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_stock');
            $table->dropIndex('idx_products_created_at');
            $table->dropIndex('idx_products_store_stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_id');
            $table->dropIndex('idx_orders_store_id');
            $table->dropIndex('idx_orders_product_id');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_store_status');
            $table->dropIndex('idx_orders_user_status');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex('idx_stores_user_id');
            $table->dropIndex('idx_stores_status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_parent_id');
            $table->dropIndex('idx_categories_slug');
            $table->dropIndex('idx_categories_featured');
            $table->dropIndex('idx_categories_sort_order');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_phone');
            $table->dropIndex('idx_users_is_seller');
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_seller_status');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('idx_carts_user_id');
            $table->dropIndex('idx_carts_product_id');
        });
    }
};
