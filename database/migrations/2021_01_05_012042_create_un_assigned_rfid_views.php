<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUnAssignedRfidViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE VIEW un_assigned_rfid AS
      (
         SELECT DISTINCT
        `r`.`id` AS `rfid_id`,
        `r`.`id` AS `id`,
        `r`.`created_at` AS `created_at`,
        `r`.`updated_at` AS `updated_at`,
        `r`.`customer_id` AS `customer_id`,
        `r`.`owner_id` AS `owner_id`,
        `h`.`operator_id` AS `operator_id`
    FROM
        (`rfid` `r`
        LEFT JOIN (SELECT
            `rfid_history`.`id` AS `id`,
                `rfid_history`.`created_at` AS `created_at`,
                `rfid_history`.`updated_at` AS `updated_at`,
                `rfid_history`.`rfid` AS `rfid`,
                `rfid_history`.`operator_id` AS `operator_id`,
                `rfid_history`.`assigned_from` AS `assigned_from`,
                `rfid_history`.`assigned_till` AS `assigned_till`,
                `latest_records`.`h_id` AS `h_id`,
                `latest_records`.`rid` AS `rid`
        FROM
            (`rfid_history`
        JOIN (SELECT
            MAX(`rfid_history`.`id`) AS `h_id`,
                `rfid_history`.`rfid` AS `rid`
        FROM
            `rfid_history`
        GROUP BY `rid`) `latest_records` ON ((`latest_records`.`h_id` = `rfid_history`.`id`)))) `h` ON ((`r`.`id` = `h`.`rfid`)))
    WHERE
        ((ISNULL(`h`.`assigned_till`)
            AND ISNULL(`h`.`assigned_from`))
            OR ((`h`.`assigned_till` IS NOT NULL)
            AND (`h`.`assigned_from` IS NOT NULL)))
            )
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('un_assigned_rfid');
    }
}
