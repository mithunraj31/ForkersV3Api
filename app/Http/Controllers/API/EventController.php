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
}
