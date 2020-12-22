<?php

namespace App\AuthValidators;

use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use App\Models\DTOs\RoleDto;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleValidator
{
    static function storeRoleValidator(RoleDto $role)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }

        // customer validation
        if ($role->customer_id) {
            $customer = Customer::find($role->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }

            throw new NoPrivilageException(['Privilage not found for requested role!']);
        }
    }
    static function getRoleByIdValidator(Role $role)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }

        // customer validation
        if (Auth::user()->customer_id === $role->customer_id) {
            return true;
        } else {

            throw new NoPrivilageException(['Privilage not found for requested role!']);
        }
    }
    static function updateRoleValidator(RoleDto $request)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            if ($request->customer_id) {
                throw new NoPrivilageException(['Privilage not found for change customer of role!']);
            }
        }
    }
    static function deleteRoleValidator(Role $role)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            $customer = Customer::find($role->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }
            throw new NoPrivilageException(['Privilage not found for requested role!']);
        }
    }
}
