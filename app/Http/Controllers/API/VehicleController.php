<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddVehicle;
use App\Http\Requests\IndexVehicle;
use App\Http\Requests\UpdateVehicle;
use App\Models\DTOs\VehicleDto;
use App\Models\Vehicle;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Services\Interfaces\VehicleServiceInterface;
use Exception;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    private DeviceServiceInterface $deviceService;
    private VehicleServiceInterface $vehicleService;
    private StonkamServiceInterface $stonkamService;

    public function __construct(
        DeviceServiceInterface $deviceService,
        StonkamServiceInterface $stonkamService,
        VehicleServiceInterface $vehicleService
    )
    {
        $this->deviceService = $deviceService;
        $this->stonkamService = $stonkamService;
        $this->vehicleService = $vehicleService;
    }

    public function index(IndexVehicle $request)
    {
        return response($this->vehicleService->getAll($request->query('perPage')), 200);
    }

    public function show(Request $request,Vehicle $vehicle)
    {
        return response($this->vehicleService->findById($vehicle), 200);
    }
    public function update(UpdateVehicle $request,Vehicle $vehicle)
    {
        $vehicleRequest = new VehicleDto();
        $vehicleRequest->name = $request->name;
        $vehicleRequest->device_id = $request->device_id;
        $vehicleRequest->group_id = $request->group_id;

        return response($this->vehicleService->update($vehicleRequest,$vehicle), 200);
    }
    public function create(AddVehicle $request)
    {
        $vehicleRequest = new VehicleDto();
        $vehicleRequest->name = $request->name;
        $vehicleRequest->device_id = $request->device_id;
        $vehicleRequest->group_id = $request->group_id;

        return response($this->vehicleService->create($vehicleRequest), 200);
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

    public function driveSummery(Request $request, $deviceId)
    {   // context start with 3 end with 2. no in context data
        // return DeviceService::getDriveSummary(2003270003, '2020-10-14 00:00:00','2020-11-02 00:00:00');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            $driveSummery = $this->deviceService->getDriveSummary($deviceId, $start, $end);
            return response($driveSummery, 200);
        } else {
            return response(['message' => 'Invalid request'], 400);
        }
    }

    public function getRoute(Request $request, $deviceId)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        if ($deviceId && $start && $end) {
            $route = $this->deviceService->getRoute($deviceId, $start, $end);
            return response($route, 200);
        } else {
            return response(['message' => 'Invalid request'], 400);
        }
    }
}
