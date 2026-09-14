<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->integer('cage_number')->default(1)->after('amount');
        });

        Schema::table('manual_feeds', function (Blueprint $table) {
            $table->integer('cage_number')->default(1)->after('duration');
        });

        Schema::table('feed_history', function (Blueprint $table) {
            $table->integer('cage_number')->default(1)->after('duration');
        });
    }

    public function down()
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->dropColumn('cage_number');
        });

        Schema::table('manual_feeds', function (Blueprint $table) {
            $table->dropColumn('cage_number');
        });

        Schema::table('feed_history', function (Blueprint $table) {
            $table->dropColumn('cage_number');
        });
    }
};