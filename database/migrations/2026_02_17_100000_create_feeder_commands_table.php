<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feeder Commands Table
     * 
     * This table queues feeding commands for the ESP32 to pick up via WiFi polling.
     * Instead of using USB serial (Python script), Laravel writes commands here
     * and the ESP32 polls /api/feeder/pending-command every 2 seconds.
     * 
     * Flow:
     * 1. Laravel creates a 'pending' command (from schedule or manual feed)
     * 2. ESP32 polls API → picks up the command
     * 3. ESP32 triggers servo → waits for duration → closes servo
     * 4. ESP32 reports done via POST /api/feeder/command-done
     * 5. Laravel marks command as 'completed' and updates schedule/manual status
     */
    public function up(): void
    {
        Schema::create('feeder_commands', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['scheduled', 'manual'])->default('manual');
            $table->unsignedBigInteger('schedule_id')->nullable();    // FK to feeding_schedules
            $table->unsignedBigInteger('manual_feed_id')->nullable(); // FK to manual_feeds
            $table->integer('duration')->default(5);                  // Servo open duration in seconds
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->timestamp('picked_up_at')->nullable();            // When ESP32 picked up the command
            $table->timestamp('completed_at')->nullable();            // When ESP32 reported done
            $table->timestamps();

            // Index for fast lookup of pending commands
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeder_commands');
    }
};
