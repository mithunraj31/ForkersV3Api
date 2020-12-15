<?php

namespace App\AuthValidators;

use App\Enum\SysRole;
use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use App\Models\DTOs\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserValidator
{
    static function validate($user, $type, $value, $request)
    {
        throw new NoPrivilageException(['Admin privilage not found!']);
    }

    static function storeUserValidator(UserDto $user)
    {

        if (AuthValidator::isAdmin()) {
            return true;
        }
        // customer validation
        if ($user->customer_id) {
            $customer = Customer::find($user->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }

            throw new NoPrivilageException(['Privilage not found for requested customer!']);
        }

        // groups validation
        $loggedUserGroups = AuthValidator::getGroups();
        $intersect = count(array_intersect($loggedUserGroups, $user->groups));
        $requestedGroupCount = count($user->groups);
        if ($intersect != $requestedGroupCount) {
            throw new NoPrivilageException(['Privilage not found for requested groups!']);
        }

        // System role validator
        if ($user->sys_role == SysRole::Admin) {
            if (!AuthValidator::isAdmin()) {
                throw new NoPrivilageException(['Privilage not found for create admin user!']);
            }
        }
    }

    static function updateUserValidator(UserDto $user)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        }
        // customer validation
        if ($user->customer_id) {
            $customer = Customer::find($user->customer_id);
            if (AuthValidator::getStkUser() === $customer->stk_user) {
                return true;
            }

            throw new NoPrivilageException(['Privilage not found for requested customer!']);
        }

        // groups validation
        if ($user->groups) {
            $loggedUserGroups = AuthValidator::getGroups();
            $intersect = count(array_intersect($loggedUserGroups, $user->groups));
            $requestedGroupCount = count($user->groups);
            if ($intersect != $requestedGroupCount) {
                throw new NoPrivilageException(['Privilage not found for requested groups!']);
            }
        }

        // System role validator
        if ($user->sys_role == SysRole::Admin) {
            if (!AuthValidator::isAdmin()) {
                throw new NoPrivilageException(['Privilage not found for create admin user!']);
            }
        }
    }
    static function getUserByIdValidator(User $user)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            $token = json_decode(Auth::token());
            if ($token->stk_user === $user->customer->stk_user) {
                return true;
            } else {
                throw new NoPrivilageException(['No privilage for view this user!']);
            }
        }
    }
    static function deleteUserValidator(User $user)
    {
        if (AuthValidator::isAdmin()) {
            return true;
        } else {
            $token = json_decode(Auth::token());
            if ($token->stk_user === $user->customer->stk_user) {
                return true;
            } else {
                throw new NoPrivilageException(['No privilage for delete this user!']);
            }
        }
    }
}
