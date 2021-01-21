<?php

namespace App\Services\Interfaces;

interface DataSummeryServiceInterface
{
    public function getEventsByOperators($start,$end, $operators);

    public function getEventsByVehicles($start,$end, $vehicles);
}
