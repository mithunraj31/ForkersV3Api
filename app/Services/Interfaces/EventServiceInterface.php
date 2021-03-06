<?php

namespace App\Services\Interfaces;

interface EventServiceInterface
{
    /**
     * the method give summary of device event,
     * count by accelerate, decelerate, eventImpact, turnLeft, turnRight and button events.
     * @param string $stkUser (optional) count only event has an username equals the value.
     * @return mixed number of each event.
     */
    public function getEventSummary($startTime,$endTime, $stkUser = null);

    public function findById($eventId);

    public function findVideoById($eventId);

    public function getAllEvent($filter);

    public function count($fiter);
}
