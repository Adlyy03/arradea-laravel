<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'store_status')) {
                $table->enum('store_status', ['open', 'closed'])->default('closed')->after('seller_otp_verified');
            }
        });

        DB::table('users')
            ->where('role', 'seller')
            ->whereNull('store_status')
            ->update(['store_status' => 'closed']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'store_status')) {
                $table->dropColumn('store_status');
            }
        });
    }
};
