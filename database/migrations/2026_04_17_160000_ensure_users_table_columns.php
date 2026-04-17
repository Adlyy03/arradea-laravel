<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add phone column if not exists
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique()->after('name');
            });
        }

        // Add phone_verified_at if not exists
        if (!Schema::hasColumn('users', 'phone_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone');
            });
        }

        // Add access_code_id if not exists
        if (!Schema::hasColumn('users', 'access_code_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('access_code_id')->nullable()->constrained()->after('wilayah');
            });
        }

        // Add is_seller if not exists
        if (!Schema::hasColumn('users', 'is_seller')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_seller')->default(false)->after('password');
            });
        }

        // Add seller_status if not exists
        if (!Schema::hasColumn('users', 'seller_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('seller_status')->default('none')->nullable()->after('is_seller');
            });
        }

        // Add seller_applied_at if not exists
        if (!Schema::hasColumn('users', 'seller_applied_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('seller_applied_at')->nullable()->after('seller_status');
            });
        }

        // Add seller_approved_at if not exists
        if (!Schema::hasColumn('users', 'seller_approved_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('seller_approved_at')->nullable()->after('seller_applied_at');
            });
        }

        // Add seller_rejected_at if not exists
        if (!Schema::hasColumn('users', 'seller_rejected_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('seller_rejected_at')->nullable()->after('seller_approved_at');
            });
        }

        // Add seller_rejection_reason if not exists
        if (!Schema::hasColumn('users', 'seller_rejection_reason')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('seller_rejection_reason')->nullable()->after('seller_rejected_at');
            });
        }

        // Add role if not exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'seller', 'buyer'])->default('buyer')->after('seller_rejection_reason');
            });
        }
    }

    public function down(): void
    {
        // No-op untuk safety
    }
};
