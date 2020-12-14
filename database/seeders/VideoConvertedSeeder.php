<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class VideoConvertedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('video_converted')->insert([
            'url' => 'https://www.google.com/imgres?imgurl=https%3A%2F%2Fwww.state.gov%2Fwp-content%2Fuploads%2F2019%2F04%2FJapan-2107x1406.jpg&imgrefurl=https%3A%2F%2Fwww.state.gov%2Fcountries-areas%2Fjapan%2F&tbnid=qiBg_asNKVBJgM&vet=12ahUKEwiQvb-E17_tAhVTZ94KHYHpDt0QMygAegUIARDQAQ..i&docid=3IgeK27GmiU3QM&w=2107&h=1406&q=japan&ved=2ahUKEwiQvb-E17_tAhVTZ94KHYHpDt0QMygAegUIARDQAQ',
        ]);
    }
}
