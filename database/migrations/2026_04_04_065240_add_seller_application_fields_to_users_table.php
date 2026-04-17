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
        // seller_status, seller_applied_at, seller_approved_at, seller_rejected_at, seller_rejection_reason
        // sudah ada di initial migration, jadi tidak perlu di-add ulang
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op karena columns dibuat di initial migration
    }
};
