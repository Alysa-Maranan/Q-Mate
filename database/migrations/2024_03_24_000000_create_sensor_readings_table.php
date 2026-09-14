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
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->float('temperature')->nullable()->comment('Temperature in Celsius');
            $table->float('humidity')->nullable()->comment('Humidity percentage');
            $table->timestamp('recorded_at')->useCurrent()->comment('When sensor reading was taken');
            $table->timestamps();
            
            // Index for faster queries on recorded_at
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
