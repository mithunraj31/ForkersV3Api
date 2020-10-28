<?php

namespace App\Services;

use App\Models\Event;
use App\Services\Interfaces\EventServiceInterface;

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
}
