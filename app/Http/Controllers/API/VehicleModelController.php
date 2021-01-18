<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\VehicleModelDto;
use App\Services\Interfaces\VehicleModelServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleModelController extends Controller
{
    private VehicleModelServiceInterface $vehicleModelService;

    public function __construct(VehicleModelServiceInterface $vehicleModelService)
    {
        $this->vehicleModelService = $vehicleModelService;
    }

    public function index()
    {
        $vehicleModel = $this->vehicleModelService->getAll();
        return response($vehicleModel, 200);
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
            'power_type' => 'required',
            'structural_method' => 'required',
            'engine_model' => 'required',
            'rated_load' => 'required',
            'fork_length' => 'required',
            'fork_width' => 'required',
            'standard_lift' => 'required',
            'maximum_lift' => 'required',
            'battery_voltage' => 'required',
            'battery_capacity' => 'required',
            'fuel_tank_capacity' => 'required',
            'body_weight' => 'required',
            'body_length' => 'required',
            'body_width' => 'required',
            'head_guard_height' => 'required',
            'min_turning_radius' => 'required',
            'ref_load_center' => 'required',
            'tire_size_front_wheel' => 'required',
            'tire_size_rear_wheel' => 'required',
            'remarks' => 'required',
        ]);
        $vehicleModel = new VehicleModelDto();
        $vehicleModel->manufacturerId = $validateVehicleModelData['manufacturer_id'];
        $vehicleModel->seriesName = $validateVehicleModelData['series_name'];
        $vehicleModel->modelName = $validateVehicleModelData['model_name'];
        $vehicleModel->powerType = $validateVehicleModelData['power_type'];
        $vehicleModel->structuralMethod = $validateVehicleModelData['structural_method'];
        $vehicleModel->engineModel = $validateVehicleModelData['engine_model'];
        $vehicleModel->ratedLoad = $validateVehicleModelData['rated_load'];
        $vehicleModel->forkLength = $validateVehicleModelData['fork_length'];
        $vehicleModel->forkWidth = $validateVehicleModelData['fork_width'];
        $vehicleModel->standardLift = $validateVehicleModelData['standard_lift'];
        $vehicleModel->maximumLift = $validateVehicleModelData['maximum_lift'];
        $vehicleModel->batteryVoltage = $validateVehicleModelData['battery_voltage'];
        $vehicleModel->batteryCapacity = $validateVehicleModelData['battery_capacity'];
        $vehicleModel->fuelTankCapacity = $validateVehicleModelData['fuel_tank_capacity'];
        $vehicleModel->bodyWeight = $validateVehicleModelData['body_weight'];
        $vehicleModel->bodyLength = $validateVehicleModelData['body_length'];
        $vehicleModel->bodyWidth = $validateVehicleModelData['body_width'];
        $vehicleModel->headGuardHeight = $validateVehicleModelData['head_guard_height'];
        $vehicleModel->minTurningRadius = $validateVehicleModelData['min_turning_radius'];
        $vehicleModel->refLoadCenter = $validateVehicleModelData['ref_load_center'];
        $vehicleModel->tireSizeFrontWheel = $validateVehicleModelData['tire_size_front_wheel'];
        $vehicleModel->tireSizeRearWheel = $validateVehicleModelData['tire_size_rear_wheel'];
        $vehicleModel->remarks = $validateVehicleModelData['remarks'];
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
            'power_type' => 'required',
            'structural_method' => 'required',
            'engine_model' => 'required',
            'rated_load' => 'required',
            'fork_length' => 'required',
            'fork_width' => 'required',
            'standard_lift' => 'required',
            'maximum_lift' => 'required',
            'battery_voltage' => 'required',
            'battery_capacity' => 'required',
            'fuel_tank_capacity' => 'required',
            'body_weight' => 'required',
            'body_length' => 'required',
            'body_width' => 'required',
            'head_guard_height' => 'required',
            'min_turning_radius' => 'required',
            'ref_load_center' => 'required',
            'tire_size_front_wheel' => 'required',
            'tire_size_rear_wheel' => 'required',
            'remarks' => 'required',
        ]);
        $vehicleModel = new VehicleModelDto();
        $vehicleModel->id = $vehicleModelId;
        $vehicleModel->manufacturerId = $validateVehicleModelData['manufacturer_id'];
        $vehicleModel->seriesName = $validateVehicleModelData['series_name'];
        $vehicleModel->modelName = $validateVehicleModelData['model_name'];
        $vehicleModel->powerType = $validateVehicleModelData['power_type'];
        $vehicleModel->structuralMethod = $validateVehicleModelData['structural_method'];
        $vehicleModel->engineModel = $validateVehicleModelData['engine_model'];
        $vehicleModel->ratedLoad = $validateVehicleModelData['rated_load'];
        $vehicleModel->forkLength = $validateVehicleModelData['fork_length'];
        $vehicleModel->forkWidth = $validateVehicleModelData['fork_width'];
        $vehicleModel->standardLift = $validateVehicleModelData['standard_lift'];
        $vehicleModel->maximumLift = $validateVehicleModelData['maximum_lift'];
        $vehicleModel->batteryVoltage = $validateVehicleModelData['battery_voltage'];
        $vehicleModel->batteryCapacity = $validateVehicleModelData['battery_capacity'];
        $vehicleModel->fuelTankCapacity = $validateVehicleModelData['fuel_tank_capacity'];
        $vehicleModel->bodyWeight = $validateVehicleModelData['body_weight'];
        $vehicleModel->bodyLength = $validateVehicleModelData['body_length'];
        $vehicleModel->bodyWidth = $validateVehicleModelData['body_width'];
        $vehicleModel->headGuardHeight = $validateVehicleModelData['head_guard_height'];
        $vehicleModel->minTurningRadius = $validateVehicleModelData['min_turning_radius'];
        $vehicleModel->refLoadCenter = $validateVehicleModelData['ref_load_center'];
        $vehicleModel->tireSizeFrontWheel = $validateVehicleModelData['tire_size_front_wheel'];
        $vehicleModel->tireSizeRearWheel = $validateVehicleModelData['tire_size_rear_wheel'];
        $vehicleModel->remarks = $validateVehicleModelData['remarks'];
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
