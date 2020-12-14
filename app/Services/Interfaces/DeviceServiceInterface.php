<?php

namespace App\Services\Interfaces;

interface DeviceServiceInterface
{
    /**
     * the method give device listings,
     * each device item contains device's details.
     */
    public function getAllDevice();

    public function getDriveSummary($deviceId, $start, $end);

    public function getRoute($deviceId, $start, $end);

}
