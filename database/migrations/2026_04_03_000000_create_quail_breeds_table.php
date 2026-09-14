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
            $table->integer('egg_production_rate')->nullable(); // eggs per day
            $table->decimal('mature_weight', 5, 2)->nullable(); // in grams
            $table->integer('maturity_age')->nullable(); // days
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quail_breeds');
    }
};