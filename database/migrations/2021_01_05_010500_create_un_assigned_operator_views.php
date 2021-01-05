<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUnAssignedOperatorViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE VIEW un_assigned_operator AS
      (
         SELECT DISTINCT
        `o`.`id` AS `operator_id`,
        `o`.`id` AS `id`,
        `o`.`created_at` AS `created_at`,
        `o`.`updated_at` AS `updated_at`,
        `o`.`name` AS `name`,
        `o`.`dob` AS `dob`,
        `o`.`address` AS `address`,
        `o`.`license_no` AS `license_no`,
        `o`.`license_received_date` AS `license_received_date`,
        `o`.`license_renewal_date` AS `license_renewal_date`,
        `o`.`license_location` AS `license_location`,
        `o`.`phone_no` AS `phone_no`,
        `o`.`deleted_at` AS `deleted_at`,
        `h`.`rfid` AS `rfid`
    FROM
        (`forkersV3_m`.`operator` `o`
        LEFT JOIN (SELECT
            `forkersV3_m`.`rfid_history`.`id` AS `id`,
                `forkersV3_m`.`rfid_history`.`created_at` AS `created_at`,
                `forkersV3_m`.`rfid_history`.`updated_at` AS `updated_at`,
                `forkersV3_m`.`rfid_history`.`rfid` AS `rfid`,
                `forkersV3_m`.`rfid_history`.`operator_id` AS `operator_id`,
                `forkersV3_m`.`rfid_history`.`assigned_from` AS `assigned_from`,
                `forkersV3_m`.`rfid_history`.`assigned_till` AS `assigned_till`,
                `latest_records`.`h_id` AS `h_id`,
                `latest_records`.`oid` AS `oid`
        FROM
            (`forkersV3_m`.`rfid_history`
        JOIN (SELECT
            MAX(`forkersV3_m`.`rfid_history`.`id`) AS `h_id`,
                `forkersV3_m`.`rfid_history`.`operator_id` AS `oid`
        FROM
            `forkersV3_m`.`rfid_history`
        GROUP BY `oid`) `latest_records` ON ((`latest_records`.`h_id` = `forkersV3_m`.`rfid_history`.`id`)))) `h` ON ((`o`.`id` = `h`.`operator_id`)))
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
        Schema::dropIfExists('un_assigned_operator');
    }
}
