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
        Schema::create('food_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->default(100); // 0-100%
            $table->string('status')->default('normal'); // normal, low, empty
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
        });

        // Insert initial record
        \DB::table('food_levels')->insert([
            'level' => 85,
            'status' => 'normal',
            'last_updated' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_levels');
    }
};
