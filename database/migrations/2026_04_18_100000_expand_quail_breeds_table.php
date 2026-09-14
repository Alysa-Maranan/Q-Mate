<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add new columns to quail_breeds table
        Schema::table('quail_breeds', function (Blueprint $table) {
            $table->string('color_markings')->nullable()->after('description');
            $table->string('size_category')->nullable()->after('color_markings'); // Small, Medium, Large
            $table->decimal('optimal_temperature', 3, 1)->nullable()->after('maturity_age'); // in Celsius
            $table->decimal('optimal_humidity', 3, 1)->nullable()->after('optimal_temperature'); // in percentage
            $table->text('care_requirements')->nullable()->after('optimal_humidity');
            $table->text('recommended_feeds')->nullable()->after('care_requirements'); // JSON array
            $table->text('common_diseases')->nullable()->after('recommended_feeds'); // JSON array
            $table->string('image_url')->nullable()->after('common_diseases');
        });
    }

    public function down()
    {
        Schema::table('quail_breeds', function (Blueprint $table) {
            $table->dropColumn(['color_markings', 'size_category', 'optimal_temperature', 'optimal_humidity', 'care_requirements', 'recommended_feeds', 'common_diseases', 'image_url']);
        });
    }
};
