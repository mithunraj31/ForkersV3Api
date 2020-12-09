<?php

namespace App\AuthValidators;

use App\Enum\SysRole;
use App\Exceptions\NoPrivilageException;
use Illuminate\Support\Facades\Auth;

class UserValidator {
    static function validate($user, $type, $value, $request)
    {
        throw new NoPrivilageException(['Admin privilage not found!']);
    }

    static function isAdmin()
    {
        $token=json_decode(Auth::token());
        if(in_array(SysRole::Admin,$token->sysRoles)){
            return true;
        } else {
            return false;
        }
    }

}
