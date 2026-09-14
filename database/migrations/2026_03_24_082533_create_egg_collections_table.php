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
        Schema::create('egg_collections', function (Blueprint $table) {
            $table->id();
            $table->string('cage_pen'); // Cage/Pen 1, 2, 3, 4
            $table->integer('total_eggs');
            $table->integer('good_eggs');
            $table->integer('cracked_eggs');
            $table->time('collection_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egg_collections');
    }
};
