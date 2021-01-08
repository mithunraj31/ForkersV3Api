<?php

namespace App\AuthValidators;

use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use App\Models\Device;
use App\Models\DTOs\VehicleDto;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class VehicleValidator
{
    static function storeVehicleValidator(VehicleDto $vehicle)
    {
        // is device in same customer
        if($vehicle->customer_id && $vehicle->device_id){
            $device = Device::find($vehicle->device_id);
            if($vehicle->customer_id != $device->customer_id){
                throw new InvalidArgumentException('Device->customer and Vehicle->customer is not the same!.');
            }
        }

        if (AuthValidator::isAdmin()) {
            return true;
        }
        // groups validation
        $loggedUserGroups = AuthValidator::getGroups();
        if (in_array($loggedUserGroups,$vehicle->group_id)) {
            return true;
        }else{
            throw new NoPrivilageException(['Privilege not found for requested group!']);
        }


    }

    public static function findByIdValidator(Vehicle $vehicle)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }
        // groups validation
        $loggedUserGroups = AuthValidator::getGroups();
        if (in_array($loggedUserGroups,$vehicle->group_id)) {
            return true;
        }else {
            throw new NoPrivilageException(['Privilege not found for requested group!']);
        }

    }

    public static function updateVehicleValidator(VehicleDto $request, Vehicle $vehicle)
    {
        // is device in same customer
        if($request->device_id){
            $device = Device::find($request->device_id);
            if($vehicle->customer_id != $device->customer_id){
                throw new InvalidArgumentException('Device->customer and Vehicle->customer is not the same!.');
            }
        }

        if (AuthValidator::isAdmin()) {
            return true;
        }
        // groups validation
        if($request->group_id){
            $loggedUserGroups = AuthValidator::getGroups();
            if (in_array($loggedUserGroups,$vehicle->group_id)) {
                return true;
            }else {
                throw new NoPrivilageException(['Privilege not found for requested group!']);
            }
        }

    }

    public static function deleteVehicleValidator(Vehicle $vehicle)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }
        // groups validation
            $loggedUserGroups = AuthValidator::getGroups();
            if (in_array($loggedUserGroups,$vehicle->group_id)) {
                return true;
            }else {
                throw new NoPrivilageException(['Privilege not found for requested group!']);
            }
    }
}
