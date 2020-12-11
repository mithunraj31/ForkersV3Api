<?php


namespace App\Models\DTOs;

class RfidHistoryDto extends DtoBase
{
    public $rfidId;

    public $driverId;

    public $beginTime;

    public $endTime;
}
