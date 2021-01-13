<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Utils\CollectionUtility;
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
        $devices = $this->deviceService->getAllDevice();
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($devices, $perPage);
        return response($result, 200);
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
