<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feeding_schedules', function (Blueprint $table) {
            $table->id();
            $table->time('time'); // HH:MM:SS
            $table->text('days')->nullable(); // JSON array of weekdays e.g. ["mon","tue"]
            $table->integer('amount')->default(1); // number of pulses/feeds
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feeding_schedules');
    }
};
