<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataSummery\AlarmsByAllOperators;
use App\Http\Requests\DataSummery\AlarmsByAllVehicles;
use App\Http\Requests\DataSummery\EventsByGroups;
use App\Http\Requests\DataSummery\EventsByOperators;
use App\Http\Requests\DataSummery\EventsByVehicles;
use App\Services\DataSummeryService;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DataSummeryController extends Controller
{
    private DataSummeryService $dataSummeryService;

    public function __construct(
        DataSummeryService $dataSummeryService
    ) {
        $this->dataSummeryService = $dataSummeryService;
    }

    public function getEventsByOperators(EventsByOperators $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $operators = explode(',', $request->operator_ids);
        $summery = $this->dataSummeryService->getEventsByOperators($start, $end, $operators);
        return response(['data' => $summery], 200);
    }
    public function getEventsByVehicles(EventsByVehicles $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $vehicles = explode(',', $request->vehicle_ids);
        $summery = $this->dataSummeryService->getEventsByVehicles($start, $end, $vehicles);
        return response(['data' => $summery], 200);
    }
    public function getEventsByGroups(EventsByGroups $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $groups = explode(',', $request->group_ids);
        $summery = $this->dataSummeryService->getEventsByGroups($start, $end, $groups);
        return response(['data' => $summery], 200);
    }
    public function getAlarmsByAllOperators(AlarmsByAllOperators $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $summery = $this->dataSummeryService->getAlarmsByAllOperators($start, $end, $request->customer_id);
        return response(['data' => $summery], 200);
    }
    public function getAlarmsByAllVehicles(AlarmsByAllVehicles $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $summery = $this->dataSummeryService->getAlarmsByAllVehicles($start, $end, $request->customer_id);
        return response(['data' => $summery], 200);
    }

}
