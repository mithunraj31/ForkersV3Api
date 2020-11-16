<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private EventServiceInterface $eventService;

    public function __construct(EventServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    public function getEventSummary(Request $request)
    {
        $stkUser = $request->query('stkUser');
        $summary = $this->eventService->getEventSummary($stkUser);

        return response()->json([
            'data' => $summary
        ], 200);
    }
    public function getEventsByDeviceId(Request $request)
    {
        $deviceId = $request->query('deviceId');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            $events = $this->eventService->getEventsByDeviceIdAndTimeRange($deviceId,$start,$end);
            return response(['data'=> $events],200);
        } else {
            return response(["message"=> "Invalid request"],400);
        }

    }
}
