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
        Schema::create('access_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaultCode = DB::table('access_codes')->insertGetId([
            'code' => 'ARRADEA2026',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'wilayah')) {
                $table->string('wilayah')->default('Arradea')->after('email');
            }

            if (! Schema::hasColumn('users', 'access_code_id')) {
                $table->foreignId('access_code_id')
                    ->nullable()
                    ->after('wilayah')
                    ->constrained('access_codes')
                    ->nullOnDelete();
            }
        });

        DB::table('users')
            ->whereNull('wilayah')
            ->update(['wilayah' => 'Arradea']);

        DB::table('users')
            ->whereNull('access_code_id')
            ->update(['access_code_id' => $defaultCode]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'access_code_id')) {
                $table->dropConstrainedForeignId('access_code_id');
            }

            if (Schema::hasColumn('users', 'wilayah')) {
                $table->dropColumn('wilayah');
            }
        });

        Schema::dropIfExists('access_codes');
    }
};
