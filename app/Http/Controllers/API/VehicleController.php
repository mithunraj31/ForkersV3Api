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
        $vehicleRequest->customer_id = $request->customer_id;
        $vehicleRequest->vehicle_number = $request->vehicle_number;
        $vehicleRequest->structural_method = $request->structural_method;
        $vehicleRequest->power_type = $request->power_type;
        $vehicleRequest->rated_load = $request->rated_load;
        $vehicleRequest->fork_length = $request->fork_length;
        $vehicleRequest->standard_lift = $request->standard_lift;
        $vehicleRequest->maximum_lift = $request->maximum_lift;
        $vehicleRequest->battery_voltage = $request->battery_voltage;
        $vehicleRequest->battery_capacity = $request->battery_capacity;
        $vehicleRequest->hour_meter_initial_value = $request->hour_meter_initial_value;
        $vehicleRequest->operating_time = $request->operating_time;
        $vehicleRequest->cumulative_uptime = $request->cumulative_uptime;
        $vehicleRequest->introduction_date = $request->introduction_date;
        $vehicleRequest->contract = $request->contract;
        $vehicleRequest->key_number = $request->key_number;
        $vehicleRequest->installation_location = $request->installation_location;
        $vehicleRequest->option1 = $request->option1;
        $vehicleRequest->option2 = $request->option2;
        $vehicleRequest->option4 = $request->option4;
        $vehicleRequest->option5 = $request->option5;
        $vehicleRequest->remarks = $request->remarks;
        $vehicleRequest->model_id = $request->model_id;

        return response($this->vehicleService->update($vehicleRequest,$vehicle), 200);
    }
    public function create(AddVehicle $request)
    {
        $vehicleRequest = new VehicleDto();
        $vehicleRequest->name = $request->name;
        $vehicleRequest->device_id = $request->device_id;
        $vehicleRequest->group_id = $request->group_id;
        $vehicleRequest->customer_id = $request->customer_id;
        $vehicleRequest->vehicle_number = $request->vehicle_number;
        $vehicleRequest->structural_method = $request->structural_method;
        $vehicleRequest->power_type = $request->power_type;
        $vehicleRequest->rated_load = $request->rated_load;
        $vehicleRequest->fork_length = $request->fork_length;
        $vehicleRequest->standard_lift = $request->standard_lift;
        $vehicleRequest->maximum_lift = $request->maximum_lift;
        $vehicleRequest->battery_voltage = $request->battery_voltage;
        $vehicleRequest->battery_capacity = $request->battery_capacity;
        $vehicleRequest->hour_meter_initial_value = $request->hour_meter_initial_value;
        $vehicleRequest->operating_time = $request->operating_time;
        $vehicleRequest->cumulative_uptime = $request->cumulative_uptime;
        $vehicleRequest->introduction_date = $request->introduction_date;
        $vehicleRequest->contract = $request->contract;
        $vehicleRequest->key_number = $request->key_number;
        $vehicleRequest->installation_location = $request->installation_location;
        $vehicleRequest->option1 = $request->option1;
        $vehicleRequest->option2 = $request->option2;
        $vehicleRequest->option4 = $request->option4;
        $vehicleRequest->option5 = $request->option5;
        $vehicleRequest->remarks = $request->remarks;
        $vehicleRequest->model_id = $request->model_id;

        return response($this->vehicleService->create($vehicleRequest), 200);
    }

    // public function doWaitingQueue($deviceId)
    // {
    //     $makers = $this->stonkamService->checkWaitingQueue($deviceId);

    //     if ($makers->count() != 0) {
    //         $makers->each(function ($maker) {
    //             try {
    //                 $this->stonkamService->makeVideo($maker);
    //             } catch (Exception $e) {
    //             }
    //         });
    //     }

    //     return response()->json([], 200);
    // }

    public function driveSummery(Request $request, $vehicleId)
    {   // context start with 3 end with 2. no in context data
        // return DeviceService::getDriveSummary(2003270003, '2020-10-14 00:00:00','2020-11-02 00:00:00');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($vehicleId && $start && $end) {
            $driveSummery = $this->vehicleService->getDriveSummary($vehicleId, $start, $end);
            return response($driveSummery, 200);
        } else {
            return response(['message' => 'Invalid request'], 400);
        }
    }

    public function getRoute(Request $request, $vehicleId)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        if ($vehicleId && $start && $end) {
            $route = $this->vehicleService->getRoute($vehicleId, $start, $end);
            return response($route, 200);
        } else {
            return response(['message' => 'Invalid request'], 400);
        }
    }
}
