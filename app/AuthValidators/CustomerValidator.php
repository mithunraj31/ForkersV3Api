<?php

namespace App\AuthValidators;

use App\Exceptions\NoPrivilageException;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerValidator
{
    static function validate()
    {
        $token = json_decode(Auth::token());
        if (!in_array('admin', $token->sysRoles)) {
            throw new NoPrivilageException(['Admin privilage not found!']);
        }
    }
    static function getByIdValidate(Customer $customer)
    {
        if (UserValidator::isAdmin()) {
            return true;
        } else {
            $token = json_decode(Auth::token());
            if ($token->stk_user === $customer->stk_user) {
                return true;
            } else {
                throw new NoPrivilageException(['No privilage for view this resource!']);
            }
        }
    }
}
