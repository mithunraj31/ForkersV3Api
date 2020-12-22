<?php

namespace App\AuthValidators;

use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use App\Models\Device;
use App\Models\DTOs\VehicleDto;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleValidator
{
    static function storeVehicleValidator(VehicleDto $vehicle)
    {

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
