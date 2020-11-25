<?php

namespace App\Services;

use App\Models\Event;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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

    public function findAll($perPage, $stkUser = null)
    {
        // Event::all()->paginate($perPage);
        return DB::table('event')->paginate($perPage);
    }


    public function findById($eventId)
    {
        $event =  Event::where('event_id', '=', $eventId)->get();
        if ($event == null) {
            throw new NotFoundResourceException();
        }
        return $event;
    }
}
