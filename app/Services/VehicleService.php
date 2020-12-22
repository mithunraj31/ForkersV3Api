<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\VehicleValidator;
use App\Http\Resources\VehicleResource;
use App\Http\Resources\VehicleResourceCollection;
use App\Models\Device;
use App\Models\DTOs\VehicleDto;
use App\Models\Vehicle;
use App\Models\VehicleDevice;
use App\Services\Interfaces\VehicleServiceInterface;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class VehicleService extends ServiceBase implements VehicleServiceInterface
{

    public function create(VehicleDto $request)
    {
        VehicleValidator::storeVehicleValidator($request);

        if($request->device_id){
            $device = Device::find($request->device_id);
            if($device->assigned){
                throw new InvalidArgumentException("Device is already assigned");
            }
        }
        $vehicle = new Vehicle();
        $vehicle->name = $request->name;
        $vehicle->group_id = $request->group_id;
        $vehicle->owner_id = Auth::user()->id;
        if($request->customer_id){
            $vehicle->customer_id = $request->customer_id;
        }else{
            $vehicle->customer_id = Auth::user()->customer_id;
        }
        $vehicle->save();
        // assign device
        if($request->device_id){
            $vehicleDevice = new VehicleDevice();
            $vehicleDevice->vehicle_id = $vehicle->id;
            $vehicleDevice->device_id = $request->device_id;
            $vehicleDevice->owner_id = Auth::user()->id;
            $vehicleDevice->save();
        }
    }

    public function update(VehicleDto $request, Vehicle $vehicle)
    {
        VehicleValidator::updateVehicleValidator($request,$vehicle);

        if($request->name){
            $vehicle->name = $request->name;
        }
        if($request->group_id){
            $vehicle->group_id =$request->group_id;
        }
        // assign or unassigned device for requested vehicle
        if($request->device_id){
            $this->assignDevice($request,$vehicle);
        }
        $vehicle->owner_id = Auth::user()->id;
        return $vehicle->save();

    }

    public function findById(Vehicle $vehicle)
    {
        VehicleValidator::findByIdValidator($vehicle);
        return new VehicleResource($vehicle->load('device'));
    }

    public function getAll($perPage=15)
    {
        if(AuthValidator::isAdmin()) {
            return new VehicleResourceCollection(Vehicle::with('device','customer')->paginate($perPage));
        }else{
            return new VehicleResourceCollection(Vehicle::whereIn('group_id',AuthValidator::getGroups())->with('device','customer')->paginate($perPage));
        }
    }

    public function delete(Vehicle $vehicle)
    {
        VehicleValidator::deleteVehicleValidator($vehicle);
        return $vehicle->delete();
    }

    private function assignDevice(VehicleDto $request, Vehicle $vehicle)
    {
        $requestedDevice = Device::find($request->device_id);
        if($requestedDevice->assigned){
            throw new InvalidArgumentException("Device is already assigned");
        }

        $vehicleDevice = new VehicleDevice();
        $vehicleDevice->vehicle_id = $vehicle->id;
        if($request->device_id === 'null'){
            $vehicleDevice->device_id = null;

        }else{
            $vehicleDevice->device_id = $request->device_id;

        }
        // unassigned previous device
        $device = $vehicle->device;
        if($device && $device->device_id){
            $deviceP = Device::find($device->device_id);
            $deviceP->assigned = false;
            $deviceP->save();
        }

        // assign current device
        $deviceC = Device::find($request->device_id);
        $deviceC->assigned = true;
        $deviceC->save();

        $vehicleDevice->owner_id = Auth::user()->id;
        $vehicleDevice->save();
    }
}
