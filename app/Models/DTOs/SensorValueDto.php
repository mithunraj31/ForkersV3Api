<?php

namespace App\Models\DTOs;

class SensorValueDto extends DtoBase
{
    public $latitude;

    public $longitude;

    public $gx;

    public $gy;

    public $gz;

    public $roll;

    public $pitch;

    public $yaw;

    public $status;

    public $direction;

    public $speed;
}
