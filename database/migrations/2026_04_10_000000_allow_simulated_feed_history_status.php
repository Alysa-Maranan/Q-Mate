<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_history', function (Blueprint $table) {
            $table->string('status')->default('completed')->change();
        });
    }

    public function down(): void
    {
        DB::table('feed_history')->where('status', 'simulated')->update(['status' => 'failed']);

        Schema::table('feed_history', function (Blueprint $table) {
            $table->enum('status', ['completed', 'failed'])->default('completed')->change();
        });
    }
};
