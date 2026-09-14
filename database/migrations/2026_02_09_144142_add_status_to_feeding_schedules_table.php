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
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->enum('status', ['idle', 'feeding_now', 'done_feeding'])->default('idle')->after('enabled');
            $table->timestamp('last_feed_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_feed_at']);
        });
    }
};
