<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->timestamp('feeding_started_at')->nullable()->after('last_feed_at');
        });
    }

    public function down(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->dropColumn('feeding_started_at');
        });
    }
};
