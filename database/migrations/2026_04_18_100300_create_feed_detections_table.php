<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feed_detections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('quail_breed_id')->nullable();
            $table->unsignedBigInteger('feed_item_id')->nullable();
            $table->string('feed_name')->nullable(); // detected feed name
            $table->decimal('confidence', 3, 2)->nullable(); // 0-1
            $table->string('compatibility_status'); // 'suitable', 'unsuitable', 'caution', 'unknown'
            $table->text('compatibility_notes')->nullable();
            $table->string('image_path')->nullable();
            $table->json('detected_attributes')->nullable(); // color, texture, etc
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quail_breed_id')->references('id')->on('quail_breeds')->onDelete('set null');
            $table->foreign('feed_item_id')->references('id')->on('feed_items')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_detections');
    }
};
