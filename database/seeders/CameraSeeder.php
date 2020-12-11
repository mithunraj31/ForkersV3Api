<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CameraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('camera')->insert([
            'device_id' => Str::random(10),
            'ch' => Str::random(10),
            'rotation' => (1),
        ]);
    }
}
