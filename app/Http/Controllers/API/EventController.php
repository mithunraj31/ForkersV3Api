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

    public function getAllEvent(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $stkUser = $request->query('stkUser');
        $summary = $this->eventService->findAll($perPage, $stkUser);

        return response()->json([
            'data' => $summary
        ], 200);
    }

    public function getEventById($eventId)
    {
        $summary = $this->eventService->findById($eventId);

        return response()->json([
            'data' => $summary
        ], 200);
    }
}
