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


    public function getEventById($eventId)
    {
        $summary = $this->eventService->findById($eventId);

        return response()->json([
            'data' => $summary
        ], 200);
    }

    public function getEventVideoById($eventId)
    {
        $summary = $this->eventService->findVideoById($eventId);

        return response()->json([
            'data' => $summary
        ], 200);
    }
    public function getEvents(Request $request)
    {
        $model = new EventFilterDto;

        $model->deviceId = $request->query('deviceId');
        $model->page = $request->query('page');
        $model->perPage = $request->query('perPage');
        $model->driverId = $request->query('driverId');
        $model->stkUser = $request->query('stkUser');
        $orderBy = $request->query('orderBy');
        if ($orderBy) {
            $model->orderBy = $orderBy;
        }

        $model->setStartDateTimeFromString((string) $request->query('start'));
        $model->setEndDateTimeFromString((string) $request->query('end'));


        $events = $this->eventService->getAllEvent($model);
        $total = $this->eventService->count($model);

        return response($events, 200);
    }
}
