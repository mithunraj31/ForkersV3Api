<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class DriveSeeder extends Seeder
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
            DB::table('drive')->insert([
                ('device_id') => $faker->word,
                ('user_name') => $faker->name,
                ('time') => $faker->date(),
                ('type') => $faker->randomDigit,
                ('driver_id') => $faker->word,
                ('status') => $faker->randomDigit,
                ('direction') => $faker->randomFloat,
                ('speed') => $faker->randomFloat,
                ('latitude') => $faker->randomFloat,
                ('longitude') => $faker->randomFloat,
            ]);
        }
    }
}
