<?php

namespace App\Models\DTOs;

class EventDto extends DtoBase
{
    public $id;

    public $eventId;

    public $deviceId;

    public $driverId;

    public $type;

    public $sensorValue;

    public $videoId;

    public $time;

    public $username;

    public $video;

    /**
     * CameraDto
     */
    public $numberOfCameras;


}
