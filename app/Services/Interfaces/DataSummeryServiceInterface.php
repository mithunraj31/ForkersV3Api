<?php

namespace App\Services\Interfaces;

interface DataSummeryServiceInterface
{
    public function getEventsByOperators($start,$end, $operators);

    public function getEventsByVehicles($start,$end, $vehicles);

    public function getEventsByGroups($start,$end, $groups);

    public function getAlarmsByAllOperators($start,$end, $customer=null);

    public function getAlarmsByAllVehicles($start,$end, $customer=null);

    public function getAlarmsByGroups($start,$end, $group_ids, $customer_id);
}
