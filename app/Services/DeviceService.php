<?php

namespace App\Services;

use App\Models\Drive;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\EventServiceInterface;

class DeviceService
{
    public static function getDriveSummary($deviceId, $startTime,$endTime){
        return Drive::getDriveSummary($deviceId, $startTime,$endTime);
    }
}
