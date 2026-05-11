<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to stores table first
        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'store_status')) {
                $table->string('store_status')->default('closed')->after('status');
            }
            if (! Schema::hasColumn('stores', 'open_time')) {
                $table->time('open_time')->nullable()->after('store_status');
            }
            if (! Schema::hasColumn('stores', 'close_time')) {
                $table->time('close_time')->nullable()->after('open_time');
            }
            if (! Schema::hasColumn('stores', 'auto_schedule')) {
                $table->boolean('auto_schedule')->default(true)->after('close_time');
            }
        });

        // Migrate data from users to stores
        DB::statement("
            UPDATE stores 
            INNER JOIN users ON stores.user_id = users.id 
            SET 
                stores.store_status = COALESCE(users.store_status, 'closed'),
                stores.open_time = users.open_time,
                stores.close_time = users.close_time,
                stores.auto_schedule = COALESCE(users.auto_schedule, 1)
            WHERE users.is_seller = 1
        ");

        // Drop columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'store_status')) {
                $table->dropColumn('store_status');
            }
            if (Schema::hasColumn('users', 'open_time')) {
                $table->dropColumn('open_time');
            }
            if (Schema::hasColumn('users', 'close_time')) {
                $table->dropColumn('close_time');
            }
            if (Schema::hasColumn('users', 'auto_schedule')) {
                $table->dropColumn('auto_schedule');
            }
        });
    }

    public function down(): void
    {
        // Restore columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'store_status')) {
                $table->string('store_status')->default('closed')->after('seller_rejection_reason');
            }
            if (! Schema::hasColumn('users', 'open_time')) {
                $table->time('open_time')->nullable()->after('store_status');
            }
            if (! Schema::hasColumn('users', 'close_time')) {
                $table->time('close_time')->nullable()->after('open_time');
            }
            if (! Schema::hasColumn('users', 'auto_schedule')) {
                $table->boolean('auto_schedule')->default(true)->after('close_time');
            }
        });

        // Migrate data back from stores to users
        DB::statement("
            UPDATE users 
            INNER JOIN stores ON stores.user_id = users.id 
            SET 
                users.store_status = stores.store_status,
                users.open_time = stores.open_time,
                users.close_time = stores.close_time,
                users.auto_schedule = stores.auto_schedule
            WHERE users.is_seller = 1
        ");

        // Drop columns from stores table
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'auto_schedule')) {
                $table->dropColumn('auto_schedule');
            }
            if (Schema::hasColumn('stores', 'close_time')) {
                $table->dropColumn('close_time');
            }
            if (Schema::hasColumn('stores', 'open_time')) {
                $table->dropColumn('open_time');
            }
            if (Schema::hasColumn('stores', 'store_status')) {
                $table->dropColumn('store_status');
            }
        });
    }
};
