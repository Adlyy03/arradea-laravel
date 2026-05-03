<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN — status is stored as string, no action needed
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'accepted', 'shipped', 'rejected', 'done', 'dibatalkan') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE orders SET status = 'accepted' WHERE status = 'shipped'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'done', 'dibatalkan') NOT NULL DEFAULT 'pending'");
    }
};
