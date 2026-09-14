<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feed_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // 'pellets', 'grains', 'vegetables', 'supplements', 'other'
            $table->decimal('protein_percentage', 4, 2)->nullable();
            $table->decimal('fat_percentage', 4, 2)->nullable();
            $table->decimal('fiber_percentage', 4, 2)->nullable();
            $table->json('suitable_breeds')->nullable(); // array of breed IDs
            $table->json('unsuitable_breeds')->nullable(); // array of breed IDs
            $table->text('health_benefits')->nullable();
            $table->text('potential_risks')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('cost_per_kg', 8, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_items');
    }
};
