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
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->structural_method = $request->structural_method;
        $vehicle->power_type = $request->power_type;
        $vehicle->rated_load = $request->rated_load;
        $vehicle->fork_length = $request->fork_length;
        $vehicle->standard_lift = $request->standard_lift;
        $vehicle->maximum_lift = $request->maximum_lift;
        $vehicle->battery_voltage = $request->battery_voltage;
        $vehicle->battery_capacity = $request->battery_capacity;
        $vehicle->hour_meter_initial_value = $request->hour_meter_initial_value;
        $vehicle->operating_time = $request->operating_time;
        $vehicle->cumulative_uptime = $request->cumulative_uptime;
        $vehicle->introduction_date = $request->introduction_date;
        $vehicle->contract = $request->contract;
        $vehicle->key_number = $request->key_number;
        $vehicle->installation_location = $request->installation_location;
        $vehicle->option1 = $request->option1;
        $vehicle->option2 = $request->option2;
        $vehicle->option3 = $request->option3;
        $vehicle->option4 = $request->option4;
        $vehicle->option5 = $request->option5;
        $vehicle->remarks = $request->remarks;
        $vehicle->model_id = $request->model_id;

        $vehicle->owner_id = Auth::user()->id;
        if($request->customer_id){
            $vehicle->customer_id = $request->customer_id;
        }else{
            $vehicle->customer_id = Auth::user()->customer_id;
        }
        $vehicle->save();
        // assign device
        if($request->device_id){
            $this->assignDevice($request, $vehicle);
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
            $paginator = Vehicle::with('device','customer')->paginate($perPage);
            $paginator->getCollection()->transform(function($value){
                return [
                    'id' =>$value->id,
                    'name' =>$value->name,
                    'owner_id'=> $value->owner_id,
                    'group_id'=> $value->group_id,
                    'customer_id'=> $value->customer_id,
                    'created_at'=>$value->created_at,
                    'updated_at'=>$value->updated_at,
                    'device' =>$value->device?$value->device->device:null,
                    'customer'=>$value->customer

                ];
             });
            return new VehicleResourceCollection($paginator);
        }else{
            $paginator = Vehicle::whereIn('group_id',AuthValidator::getGroups())->with('device','customer')->paginate($perPage);
            $paginator->getCollection()->transform(function($value){
                return [
                    'id' =>$value->id,
                    'name' =>$value->name,
                    'owner_id'=> $value->owner_id,
                    'group_id'=> $value->group_id,
                    'customer_id'=> $value->customer_id,
                    'created_at'=>$value->created_at,
                    'updated_at'=>$value->updated_at,
                    'device' =>$value->device?$value->device->device:null,
                    'customer'=>$value->customer

                ];
             });
            return new VehicleResourceCollection($paginator);
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
        if($device && $device->id){
            $deviceP = Device::find($device->id);
            $deviceP->assigned = false;
            $deviceP->save();

            // add null record to vehicle_device tabble
            $vehicleDevice = new VehicleDevice();
            $vehicleDevice->vehicle_id = null;
            $vehicleDevice->device_id = $device->id;
            $vehicleDevice->owner_id = Auth::user()->id;
            $vehicleDevice->save();
        }

        // assign current device
        $deviceC = Device::find($request->device_id);
        $deviceC->assigned = true;
        $deviceC->save();

        $vehicleDevice->owner_id = Auth::user()->id;
        $vehicleDevice->save();
    }
}
