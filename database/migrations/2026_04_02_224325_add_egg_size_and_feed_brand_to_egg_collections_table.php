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
        Schema::table('egg_collections', function (Blueprint $table) {
            $table->string('egg_size')->default('mixed')->after('notes');
            $table->string('feed_brand')->default('Quail Layer Smash')->after('egg_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egg_collections', function (Blueprint $table) {
            $table->dropColumn(['egg_size', 'feed_brand']);
        });
    }
};
