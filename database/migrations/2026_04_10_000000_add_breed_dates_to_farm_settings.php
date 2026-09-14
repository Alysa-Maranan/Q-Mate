<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('farm_settings')->insertOrIgnore([
            ['key' => 'breed_start_date', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'breed_end_date',   'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        DB::table('farm_settings')->whereIn('key', ['breed_start_date', 'breed_end_date'])->delete();
    }
};
