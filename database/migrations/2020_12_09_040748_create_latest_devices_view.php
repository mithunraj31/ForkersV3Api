<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLatestDevicesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW latest_deivces AS
            SELECT
                d.device_id AS device_id,
                d.stk_user AS stk_user,
                reg.driver_id AS latest_driver_id,
                IF(reg.type = 3, false, true) AS is_online,
                reg.latitude as latitude,
                reg.longitude as longitude
            FROM device d INNER JOIN
                (SELECT
                    r.id,
                    r.device_id,
                    r.driver_id,
                    r.type,
                    r.latitude,
                    r.longitude
                FROM regular r INNER JOIN
                    (SELECT MAX(id) AS latest_id
                    FROM regular
                    GROUP BY device_id) gr
                ON r.id = gr.latest_id) reg
            ON d.device_id = reg.device_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW latest_deivces');
    }
}
