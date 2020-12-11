<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role')->insert([
            'description' => 'ADMIN',
            'role_name' => 'ADMIN',
        ]);

        DB::table('role')->insert([
            'description' => 'USER',
            'role_name' => 'USER',
        ]);

        DB::table('role')->insert([
            'description' => 'CUSTOMER',
            'role_name' => 'CUSTOMER',
        ]);
    }
}
