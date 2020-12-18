<?php

namespace App\AuthValidators;

use App\Enum\AccessType;
use App\Enum\ResourceType;
use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use App\Models\DTOs\GroupDto;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupValidator {
    static function storeGroupValidator(GroupDto $group)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }

        // customer validation
        if ($group->customer_id) {
            $customer = Customer::find($group->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }

            throw new NoPrivilageException(['Privilege not found for requested group!']);
        }
    }
    static function getGroupByIdValidator(Group $group)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }

        // customer validation
        if (Auth::user()->customer_id === $group->customer_id) {
            return true;
        } else {

            throw new NoPrivilageException(['Privilege not found for requested group!']);
        }
    }
    static function updateGroupValidator(GroupDto $request)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            if ($request->customer_id) {
                throw new NoPrivilageException(['Privilege not found for change customer of group!']);
            }
        }
    }
    static function deleteGroupValidator(Group $group)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            $customer = Customer::find($group->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }
            throw new NoPrivilageException(['Privilege not found for requested group!']);
        }
    }

}
