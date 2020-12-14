<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class MakeVideoWaitingQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('make_video_waiting_queue')->insert([
                ('device_id') => $faker->word,
                ('begin_datetime') => $faker->dateTime,
                ('end_datetime') => $faker->dateTime,
                ('username') => $faker->name,
            ]);
        }
    }
}
