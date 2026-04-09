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
            $table->enum('seller_status', ['none', 'pending', 'approved', 'rejected'])->default('none');
            $table->timestamp('seller_applied_at')->nullable();
            $table->timestamp('seller_approved_at')->nullable();
            $table->timestamp('seller_rejected_at')->nullable();
            $table->text('seller_rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'seller_status',
                'seller_applied_at',
                'seller_approved_at',
                'seller_rejected_at',
                'seller_rejection_reason',
            ]);
        });
    }
};
