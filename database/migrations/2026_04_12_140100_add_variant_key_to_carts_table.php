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
        if (! Schema::hasColumn('carts', 'variant_key')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->string('variant_key')->default('default')->after('product_id');
            });
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if ($this->indexExists('carts', 'carts_user_id_product_id_variant_key_unique')) {
            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropUnique(['user_id', 'product_id']);

            $table->index('user_id');
            $table->index('product_id');
            $table->unique(['user_id', 'product_id', 'variant_key']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            if (Schema::hasColumn('carts', 'variant_key')) {
                Schema::table('carts', function (Blueprint $table) {
                    $table->dropColumn('variant_key');
                });
            }

            return;
        }

        if (! $this->indexExists('carts', 'carts_user_id_product_id_variant_key_unique')) {
            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropUnique(['user_id', 'product_id', 'variant_key']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn('variant_key');
            $table->unique(['user_id', 'product_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $indexName]
        );

        return ((int) ($result->aggregate ?? 0)) > 0;
    }
};
