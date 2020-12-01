<?php

namespace App\Models\DTOs;

use Carbon\Carbon;

class EventFilterDto extends DtoBase
{
    public $deviceId;

    public $startDatetime;

    public $endDatetime;

    public $page;

    public $perPage;

    public $orderBy = 'desc';

    public $stkUser;

    public $driverId;

    public function setStartDateTimeFromString(string $dateTimeStr)
    {
        if ($dateTimeStr != null && $dateTimeStr != '') {
            $this->startDatetime = Carbon::parse($dateTimeStr);
        }
    }

    public function setEndDateTimeFromString(string $dateTimeStr)
    {
        if ($dateTimeStr != null && $dateTimeStr != '') {
            $this->endDateTime = Carbon::parse($dateTimeStr);
        }
    }

    public function getStartDateTimeUtc() {
        return $this->startDatetime->timezone('UTC');
    }

    public function getEndDateTimeUtc() {
        return $this->endDateTime->timezone('UTC');
    }
}
