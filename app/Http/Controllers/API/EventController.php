<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\EventFilterDto;
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

    public function getEvents(Request $request)
    {
        $filter = new EventFilterDto();

        $filter->deviceId = $request->query('deviceId');
        $filter->page = $request->query('page');
        $filter->perPage = $request->query('perPage');
        $filter->setStartDateTimeFromString((string) $request->query('start'));
        $filter->setEndDateTimeFromString((string) $request->query('end'));

        $events = $this->eventService->getAllEvent($filter);
        return response()->json([
            'data' => $events
        ], 200);
    }
}
