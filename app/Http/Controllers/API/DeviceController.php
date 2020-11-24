<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
}
