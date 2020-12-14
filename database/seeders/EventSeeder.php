<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
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
            DB::table('event')->insert([
                ('event_id') => $faker->uuid,
                ('device_id') => $faker->word,
                ('driver_id') => $faker->word,
                ('type') => $faker->randomDigit,
                ('latitude') => 35.6804,
                ('longitude') => 139.7690,
                ('gx') => $faker->randomFloat,
                ('gy') => $faker->randomFloat,
                ('gz') => $faker->randomFloat,
                ('roll') => $faker->randomFloat,
                ('pitch') => $faker->word,
                ('yaw') => $faker->date,
                ('status') => $faker->date,
                ('direction') => (1),
                ('speed') => $faker->word,
                ('video_id') => $faker->randomDigit,
                ('time') => $faker->date,
                ('username') => $faker->name,
            ]);
        }
    }
}
