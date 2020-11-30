<?php

namespace App\Models\DTOs;

use Carbon\Carbon;

class EventFilterDto
{
    public $deviceId;

    public $startDatetime;

    public $endDatetime;

    public $page;

    public $perPage;

    public $orderBy = 'asc';

    public $stkUser;

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
