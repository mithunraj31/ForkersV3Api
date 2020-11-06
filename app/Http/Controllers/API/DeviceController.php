<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\Interfaces\DeviceServiceInterface;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    private DeviceServiceInterface $deviceService;

    public function __construct(DeviceServiceInterface $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index(Request $request)
    {
        $page = $request->query('page') ? (int)$request->query('page') : 1;
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $devices = $this->deviceService->getAllDevice();

        $pageItems = $devices->forPage($page, $perPage);
        return [
            'data' => $pageItems,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($devices)
            ]
        ];
    }

    public function driveSummery(Request $request)
    {   // context start with 3 end with 2. no in context data
        // return DeviceService::getDriveSummary(2003270003, '2020-10-14 00:00:00','2020-11-02 00:00:00');
        $deviceId = $request->query('deviceId');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            return DeviceService::getDriveSummary($deviceId, $start, $end);
        } else {
            return response(["message"=> "Invalid request"],400);
        }
    }
}
