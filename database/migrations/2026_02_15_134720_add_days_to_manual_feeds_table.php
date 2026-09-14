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
        Schema::table('manual_feeds', function (Blueprint $table) {
            $table->text('days')->nullable()->after('duration'); // JSON array of weekdays
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_feeds', function (Blueprint $table) {
            $table->dropColumn('days');
        });
    }
};
