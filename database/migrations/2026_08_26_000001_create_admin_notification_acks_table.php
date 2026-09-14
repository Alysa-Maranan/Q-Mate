<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores which dashboard notifications (orders, reviews, chats,
     * cancellations) have been acknowledged per admin user so the
     * state is consistent across devices and browsers.
     */
    public function up(): void
    {
        Schema::create('admin_notification_acks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->string('ack_key', 150);
            $table->timestamps();

            $table->unique(['user_id', 'ack_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notification_acks');
    }
};