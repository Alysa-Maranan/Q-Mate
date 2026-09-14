<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detection_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('quail_breed_id')->nullable();
            $table->string('detection_type'); // 'quail', 'human', 'unknown'
            $table->decimal('confidence', 3, 2)->nullable(); // 0-1
            $table->string('image_path')->nullable();
            $table->text('detection_notes')->nullable();
            $table->string('location')->nullable(); // where detected (cage, pen, etc)
            $table->json('raw_detection_data')->nullable(); // store raw model output
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quail_breed_id')->references('id')->on('quail_breeds')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detection_histories');
    }
};
