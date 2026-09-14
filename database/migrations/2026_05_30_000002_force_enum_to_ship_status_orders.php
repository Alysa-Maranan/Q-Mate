<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the ENUM for status column to add 'to_ship'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'to_ship', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert ENUM to original
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
