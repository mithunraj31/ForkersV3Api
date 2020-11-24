<?php
namespace App\Models\DTOs;

use Carbon\Carbon;

class VideoMaker extends DtoBase
{
    public string $stonkamUsername;

    public Carbon $beginDateTime;

    public Carbon $endDateTime;

    public int  $deviceId;

    public function setBeginDateTimeFromString(string $dateTimeStr)
    {
        $this->beginDateTime = Carbon::parse($dateTimeStr);
    }

    public function setEndDateTimeFromString(string $dateTimeStr)
    {
        $this->endDateTime = Carbon::parse($dateTimeStr);
    }

    public function getBeginDateTimeUtc() {
        return $this->beginDateTime->timezone('UTC');
    }

    public function getEndDateTimeUtc() {
        return $this->endDateTime->timezone('UTC');
    }
}
