<?php

namespace App\Services;

use App\Models\DTOs\VehicleModelDto;
use App\Models\VehicleModel;
use App\Services\Interfaces\VehicleModelServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class VehicleModelService extends ServiceBase implements VehicleModelServiceInterface
{
    public function create(VehicleModelDto $request)
    {
        $vehicleModel = new VehicleModel();
        $vehicleModel->manufacturer_id = $request->manufacturerId;
        $vehicleModel->series_name = $request->seriesName;
        $vehicleModel->model_name = $request->modelName;
        $vehicleModel->power_type = $request->powerType;
        $vehicleModel->structural_method = $request->structuralMethod;
        $vehicleModel->engine_model = $request->engineModel;
        $vehicleModel->rated_load = $request->ratedLoad;
        $vehicleModel->fork_length = $request->forkLength;
        $vehicleModel->fork_width = $request->forkWidth;
        $vehicleModel->standard_lift = $request->standardLift;
        $vehicleModel->maximum_lift = $request->maximumLift;
        $vehicleModel->battery_voltage = $request->batteryVoltage;
        $vehicleModel->battery_capacity = $request->batteryCapacity;
        $vehicleModel->fuel_tank_capacity = $request->fuelTankCapacity;
        $vehicleModel->body_weight = $request->bodyWeight;
        $vehicleModel->body_length = $request->bodyLength;
        $vehicleModel->body_width = $request->bodyWidth;
        $vehicleModel->head_guard_height = $request->headGuardHeight;
        $vehicleModel->min_turning_radius = $request->minTurningRadius;
        $vehicleModel->ref_load_center = $request->refLoadCenter;
        $vehicleModel->tire_size_front_wheel = $request->tireSizeFrontWheel;
        $vehicleModel->tire_size_rear_wheel = $request->tireSizeRearWheel;
        $vehicleModel->remarks = $request->remarks;
        $vehicleModel->owner_Id = $request->ownerId;
        $vehicleModel->save();
    }

    public function update(VehicleModelDto $request)
    {
        $vehicleModel = $this->findById($request->id);
        $vehicleModel->manufacturer_id = $request->manufacturerId;
        $vehicleModel->series_name = $request->seriesName;
        $vehicleModel->model_name = $request->modelName;
        $vehicleModel->power_type = $request->powerType;
        $vehicleModel->structural_method = $request->structuralMethod;
        $vehicleModel->engine_model = $request->engineModel;
        $vehicleModel->rated_load = $request->ratedLoad;
        $vehicleModel->fork_length = $request->forkLength;
        $vehicleModel->fork_width = $request->forkWidth;
        $vehicleModel->standard_lift = $request->standardLift;
        $vehicleModel->maximum_lift = $request->maximumLift;
        $vehicleModel->battery_voltage = $request->batteryVoltage;
        $vehicleModel->battery_capacity = $request->batteryCapacity;
        $vehicleModel->fuel_tank_capacity = $request->fuelTankCapacity;
        $vehicleModel->body_weight = $request->bodyWeight;
        $vehicleModel->body_length = $request->bodyLength;
        $vehicleModel->body_width = $request->bodyWidth;
        $vehicleModel->head_guard_height = $request->headGuardHeight;
        $vehicleModel->min_turning_radius = $request->minTurningRadius;
        $vehicleModel->ref_load_center = $request->refLoadCenter;
        $vehicleModel->tire_size_front_wheel = $request->tireSizeFrontWheel;
        $vehicleModel->tire_size_rear_wheel = $request->tireSizeRearWheel;
        $vehicleModel->remarks = $request->remarks;
        $vehicleModel->owner_Id = $request->ownerId;
        $vehicleModel->update();
    }

    public function findById($vehicleModelId)
    {
        $vehicleModel =  VehicleModel::find($vehicleModelId);
        if ($vehicleModel == null) {
            Log::warning("Not found VehicleModel by ID $vehicleModelId");
            throw new NotFoundResourceException();
        }
        return $vehicleModel;
    }

    public function getAll()
    {
        $vehicleModel =  VehicleModel::all();
        if ($vehicleModel == null) {
            Log::warning("Not found VehicleModel ");
            throw new NotFoundResourceException();
        }
        return $vehicleModel;
    }

    public function delete($vehicleModelId)
    {
        $vehicleModel = $this->findById($vehicleModelId);
        Log::info('Deleting vehicleModel data', (array)  $vehicleModelId);
        $vehicleModel->delete();
        Log::info("Deleted vehicleModel by ID $vehicleModelId");
    }
}
