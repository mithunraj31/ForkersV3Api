<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class RegularSeeder extends Seeder
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
            DB::table('regular')->insert([
                ('device_id') => $faker->word,
                ('username') => $faker->name,
                ('driver_id') => $faker->word,
                ('type') => $faker->randomDigit,
                ('time') => $faker->date,
                ('status') => $faker->date,
                ('direction') => (1),
                ('latitude') => 35.6804,
                ('longitude') => 139.7690,
            ]);
        }
    }
}
