<?php


namespace App\Models\DTOs;

class RfidHistoryDto extends DtoBase
{
    public $rfid;

    public $operatorId;

    public $assignedFrom;

    public $assignedTill;
}
