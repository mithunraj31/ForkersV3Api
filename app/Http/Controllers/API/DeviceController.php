<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\DeviceServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    private DeviceServiceInterface $deviceService;

    private StonkamServiceInterface $stonkamService;

    public function __construct(DeviceServiceInterface $deviceService,
    StonkamServiceInterface $stonkamService)
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

    public function doWaitingQueue($deiviceId)
    {
        $makers = $this->stonkamService->checkWaitingQueue($deiviceId);

        if ($makers->count() != 0) {
            $makers->each(function ($maker) {
                try {
                    $this->stonkamService->makeVideo($maker);
                } catch (Exception $e) {}

            });
        }

        return response([], 200);
    }
}
