<?php

namespace App\Models\DTOs;

class EventDto extends DtoBase
{
    public $id;

    public $eventId;

    public $deviceId;

    public $driverId;

    public $type;

    public SensorValueDto $sensorValue;

    public $videoId;

    public $time;

    public $username;

    public VideoDto $video;

    /**
     * CameraDto
     */
    public $numberOfCameras;


}
