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
        Schema::create('feed_history', function (Blueprint $table) {
            $table->id();
            $table->enum('feed_type', ['scheduled', 'manual']); // Type of feeding
            $table->foreignId('schedule_id')->nullable()->constrained('feeding_schedules')->onDelete('set null'); // Link to schedule if applicable
            $table->foreignId('manual_feed_id')->nullable()->constrained('manual_feeds')->onDelete('set null'); // Link to manual feed if applicable
            $table->integer('duration'); // Duration in seconds
            $table->text('days')->nullable(); // Days for manual feed (JSON array)
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->timestamp('fed_at'); // When the feeding occurred
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_history');
    }
};
