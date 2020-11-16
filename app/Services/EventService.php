<?php

namespace App\Services;

use App\Models\Event;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Support\Facades\DB;

class EventService implements EventServiceInterface
{
    /**
     * the method give summary of device event,
     * count by accelerate, decelerate, eventImpact, turnLeft, turnRight and button events.
     * @param string $stkUser (optional) count only event has an username equals the value.
     * @return mixed number of each event.
     */
    public function getEventSummary($stkUser = null)
    {
        return Event::getEventSummary($stkUser);
    }

    public function getEventsByDeviceIdAndTimeRange($deviceId, $start, $end)
    {
        return DB::table('event')->where([['device_id','=',$deviceId]])->whereBetween('time', [$start, $end])->orderBy('time', 'asc')->get();
    }
}
