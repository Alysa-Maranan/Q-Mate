<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quail_breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('egg_production_rate')->nullable();
            $table->decimal('mature_weight', 5, 2)->nullable();
            $table->integer('maturity_age')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('color_markings')->nullable();
            $table->string('size_category')->nullable();
            $table->decimal('optimal_temperature', 4, 1)->nullable();
            $table->decimal('optimal_humidity', 4, 1)->nullable();
            $table->text('care_requirements')->nullable();
            $table->json('recommended_feeds')->nullable();
            $table->json('common_diseases')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quail_breeds');
    }
};