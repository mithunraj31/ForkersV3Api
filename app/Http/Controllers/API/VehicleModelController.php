<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\VehicleModelDto;
use App\Services\Interfaces\VehicleModelServiceInterface;
use App\Utils\CollectionUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleModelController extends Controller
{
    private VehicleModelServiceInterface $vehicleModelService;

    public function __construct(VehicleModelServiceInterface $vehicleModelService)
    {
        $this->vehicleModelService = $vehicleModelService;
    }

    public function index(Request $request)
    {
        $vehicleModel = $this->vehicleModelService->getAll();

        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($vehicleModel, $perPage);
        return response($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateVehicleModelData = $request->validate([
            'manufacturer_id' => 'required',
            'series_name' => 'required',
            'model_name' => 'required',
        ]);
        $vehicleModel = new VehicleModelDto();
        $vehicleModel->manufacturerId = $validateVehicleModelData['manufacturer_id'];
        $vehicleModel->seriesName = $validateVehicleModelData['series_name'];
        $vehicleModel->modelName = $validateVehicleModelData['model_name'];
        $vehicleModel->powerType = $request->power_type;
        $vehicleModel->structuralMethod = $request->structural_method;
        $vehicleModel->engineModel = $request->engine_model;
        $vehicleModel->ratedLoad = $request->rated_load;
        $vehicleModel->forkLength = $request->fork_length;
        $vehicleModel->forkWidth = $request->fork_width;
        $vehicleModel->standardLift = $request->standard_lift;
        $vehicleModel->maximumLift = $request->maximum_lift;
        $vehicleModel->batteryVoltage = $request->battery_voltage;
        $vehicleModel->batteryCapacity = $request->battery_capacity;
        $vehicleModel->fuelTankCapacity = $request->fuel_tank_capacity;
        $vehicleModel->bodyWeight = $request->body_weight;
        $vehicleModel->bodyLength = $request->body_length;
        $vehicleModel->bodyWidth = $request->body_width;
        $vehicleModel->headGuardHeight = $request->head_guard_height;
        $vehicleModel->minTurningRadius = $request->min_turning_radius;
        $vehicleModel->refLoadCenter = $request->ref_load_center;
        $vehicleModel->tireSizeFrontWheel = $request->tire_size_front_wheel;
        $vehicleModel->tireSizeRearWheel = $request->tire_size_rear_wheel;
        $vehicleModel->remarks = $request->remarks;
        $vehicleModel->ownerId = Auth::user()->id;
        $this->vehicleModelService->create($vehicleModel);
        return response(['message' => 'Success!'], 200);
    }


    public function show($vehicleModelId)
    {
        $vehicleModel = $this->vehicleModelService->findById($vehicleModelId);
        return response([
            'data' => $vehicleModel
        ], 200);
    }
    public function update(Request $request, $vehicleModelId)
    {
        $validateVehicleModelData = $request->validate([
            'manufacturer_id' => 'required',
            'series_name' => 'required',
            'model_name' => 'required',
        ]);
        $vehicleModel = new VehicleModelDto();
        $vehicleModel->id = $vehicleModelId;
        $vehicleModel->manufacturerId = $validateVehicleModelData['manufacturer_id'];
        $vehicleModel->seriesName = $validateVehicleModelData['series_name'];
        $vehicleModel->modelName = $validateVehicleModelData['model_name'];
        $vehicleModel->powerType = $request->power_type;
        $vehicleModel->structuralMethod = $request->structural_method;
        $vehicleModel->engineModel = $request->engine_model;
        $vehicleModel->ratedLoad = $request->rated_load;
        $vehicleModel->forkLength = $request->fork_length;
        $vehicleModel->forkWidth = $request->fork_width;
        $vehicleModel->standardLift = $request->standard_lift;
        $vehicleModel->maximumLift = $request->maximum_lift;
        $vehicleModel->batteryVoltage = $request->battery_voltage;
        $vehicleModel->batteryCapacity = $request->battery_capacity;
        $vehicleModel->fuelTankCapacity = $request->fuel_tank_capacity;
        $vehicleModel->bodyWeight = $request->body_weight;
        $vehicleModel->bodyLength = $request->body_length;
        $vehicleModel->bodyWidth = $request->body_width;
        $vehicleModel->headGuardHeight = $request->head_guard_height;
        $vehicleModel->minTurningRadius = $request->min_turning_radius;
        $vehicleModel->refLoadCenter = $request->ref_load_center;
        $vehicleModel->tireSizeFrontWheel = $request->tire_size_front_wheel;
        $vehicleModel->tireSizeRearWheel = $request->tire_size_rear_wheel;
        $vehicleModel->remarks = $request->remarks;
        $vehicleModel->ownerId = Auth::user()->id;
        $this->vehicleModelService->update($vehicleModel);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($vehicleModelId)
    {
        $this->vehicleModelService->delete($vehicleModelId);
        return response(['message' => 'Success!'], 200);
    }
}
