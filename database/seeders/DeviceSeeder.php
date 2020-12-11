<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
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
            DB::table('device')->insert([
                ('device_id') => $faker->name,
                ('plate_number') => $faker->word,
                ('scan_code') => $faker->word,
                ('channel_number') => $faker->randomDigit,
                ('group_name') => $faker->word,
                ('tcp_server_addr') => $faker->word,
                ('tcp_stream_out_port') => $faker->randomDigit,
                ('udp_server_addr') => $faker->word,
                ('udp_stream_out_port') => $faker->randomDigit,
                ('net_type') => $faker->randomDigit,
                ('device_type') => $faker->word,
                ('create_time') => $faker->date,
                ('update_time') => $faker->date,
                ('is_active') => (1),
                ('stk_user') => $faker->word,
                ('deleted_at') => $faker->date,
            ]);
        }
    }
}
