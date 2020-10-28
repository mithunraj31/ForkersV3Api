<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'PONGPEERA',
            'email' => 'mbel003@mbel.co.jp',
            'password' => Hash::make('Mbel2020')
        ]);

        DB::table('users')->insert([
            'name' => 'MITHUNRAJ',
            'email' => 'mbel001@mbel.co.jp',
            'password' => Hash::make('Mbel2020')
        ]);

        DB::table('users')->insert([
            'name' => 'LASITHA',
            'email' => 'mbel002@mbel.co.jp',
            'password' => Hash::make('Mbel2020')
        ]);
    }
}
