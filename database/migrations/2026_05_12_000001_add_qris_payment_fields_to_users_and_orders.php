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
            if (! Schema::hasColumn('users', 'qris_image')) {
                $table->string('qris_image')->nullable()->after('preferred_mode');
            }

            if (! Schema::hasColumn('users', 'payment_name')) {
                $table->string('payment_name')->nullable()->after('qris_image');
            }

            if (! Schema::hasColumn('users', 'payment_type')) {
                $table->string('payment_type', 50)->nullable()->after('payment_name');
            }

            if (! Schema::hasColumn('users', 'payment_number')) {
                $table->string('payment_number', 100)->nullable()->after('payment_type');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 30)->default('cod')->after('total_price');
            }

            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 30)->default('pending')->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_proof');
            }

            if (! Schema::hasColumn('orders', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('paid_at');
            }
        });

        if (Schema::hasColumn('orders', 'status')) {
            // First, change ENUM to VARCHAR to allow any value temporarily
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement("ALTER TABLE orders MODIFY status VARCHAR(30) NOT NULL DEFAULT 'pending'");
            }
            
            // Then update the values
            DB::table('orders')->update([
                'status' => DB::raw("CASE status WHEN 'accepted' THEN 'processing' WHEN 'done' THEN 'completed' WHEN 'rejected' THEN 'cancelled' WHEN 'dibatalkan' THEN 'cancelled' ELSE status END"),
            ]);

            // Finally, set it back to ENUM with new values
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'status') && DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','accepted','rejected','done') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
            if (Schema::hasColumn('orders', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('orders', 'payment_proof')) {
                $table->dropColumn('payment_proof');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'payment_number')) {
                $table->dropColumn('payment_number');
            }
            if (Schema::hasColumn('users', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('users', 'payment_name')) {
                $table->dropColumn('payment_name');
            }
            if (Schema::hasColumn('users', 'qris_image')) {
                $table->dropColumn('qris_image');
            }
        });
    }
};