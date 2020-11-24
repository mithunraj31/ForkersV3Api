<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Exception;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    private DeviceServiceInterface $deviceService;

    private StonkamServiceInterface $stonkamService;

    public function __construct(
        DeviceServiceInterface $deviceService,
        StonkamServiceInterface $stonkamService
    ) {
        $this->deviceService = $deviceService;
        $this->stonkamService = $stonkamService;
    }

    public function index(Request $request)
    {
        $page = $request->query('page') ? (int)$request->query('page') : 1;
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;

        $devices = $this->deviceService->getAllDevice();

        $pageItems = $devices->forPage($page, $perPage);

        return response()->json([
            'data' => $pageItems,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($devices)
            ]
        ], 200);
    }

    public function doWaitingQueue($deviceId)
    {
        $makers = $this->stonkamService->checkWaitingQueue($deviceId);

        if ($makers->count() != 0) {
            $makers->each(function ($maker) {
                try {
                    $this->stonkamService->makeVideo($maker);
                } catch (Exception $e) {
                }
            });
        }

        return response()->json([], 200);
    }

    public function driveSummery(Request $request,$deviceId)
    {   // context start with 3 end with 2. no in context data
        // return DeviceService::getDriveSummary(2003270003, '2020-10-14 00:00:00','2020-11-02 00:00:00');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            $driveSummery = $this->deviceService->getDriveSummary($deviceId,$start,$end);
            return response($driveSummery,200);
        } else {
            return response(['message'=> 'Invalid request'],400);
        }
    }

    public function getRoute(Request $request, $deviceId)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            $route = $this->deviceService->getRoute($deviceId,$start,$end);
            return response($route,200);
        } else {
            return response(['message'=> 'Invalid request'],400);
        }
    }
}
