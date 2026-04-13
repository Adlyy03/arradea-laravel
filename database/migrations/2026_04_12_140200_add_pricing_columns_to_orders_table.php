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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('variant_key')->default('default')->after('product_id');
            $table->decimal('unit_price_original', 12, 2)->nullable()->after('quantity');
            $table->decimal('unit_price_final', 12, 2)->nullable()->after('unit_price_original');
            $table->decimal('discount_percent_applied', 5, 2)->default(0)->after('unit_price_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['variant_key', 'unit_price_original', 'unit_price_final', 'discount_percent_applied']);
        });
    }
};
