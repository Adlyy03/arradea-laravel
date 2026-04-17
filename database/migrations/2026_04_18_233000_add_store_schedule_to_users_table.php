<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'auto_schedule')) {
                $table->dropColumn('auto_schedule');
            }

            if (Schema::hasColumn('users', 'close_time')) {
                $table->dropColumn('close_time');
            }

            if (Schema::hasColumn('users', 'open_time')) {
                $table->dropColumn('open_time');
            }
        });
    }
};