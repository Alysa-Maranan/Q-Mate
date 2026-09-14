<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_levels', function (Blueprint $table) {
            $table->integer('distance')->nullable()->after('level')->comment('Raw distance from ultrasonic sensor in cm');
        });
    }

    public function down(): void
    {
        Schema::table('food_levels', function (Blueprint $table) {
            $table->dropColumn('distance');
        });
    }
};