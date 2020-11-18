<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\DeviceServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
