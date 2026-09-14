<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('score')->unsigned();
            $table->text('meta')->nullable();
            $table->timestamps();
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_scores');
    }
};
