<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_history', function (Blueprint $table) {
            $table->string('feed_brand')->nullable()->after('feed_type');
        });
    }

    public function down(): void
    {
        Schema::table('feed_history', function (Blueprint $table) {
            $table->dropColumn('feed_brand');
        });
    }
};
