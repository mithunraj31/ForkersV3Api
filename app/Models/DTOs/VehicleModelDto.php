<?php


namespace App\Models\DTOs;

class VehicleModelDto extends DtoBase
{
    public $id;
    public $name;
    public $description;
    public $manufacturerId;
    public $seriesName;
    public $modelName;
    public $powerType;
    public $structuralMethod;
    public $engineModel;
    public $ratedLoad;
    public $forkLength;
    public $forkWidth;
    public $standardLift;
    public $maximumLift;
    public $batteryVoltage;
    public $batteryCapacity;
    public $fuelTankCapacity;
    public $bodyWeight;
    public $bodyLength;
    public $bodyWidth;
    public $headGuardHeight;
    public $minTurningRadius;
    public $refLoadCenter;
    public $tireSizeFrontWheel;
    public $tireSizeRearWheel;
    public $remarks;
    public $ownerId;
}
