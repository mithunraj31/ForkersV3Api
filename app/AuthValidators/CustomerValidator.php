<?php

namespace App\AuthValidators;

use App\Exceptions\NoPrivilageException;
use Illuminate\Support\Facades\Auth;

class CustomerValidator {
    static function validate()
    {
        $token = json_decode(Auth::token());
        if(!in_array('admin',$token->sysRoles)){
            throw new NoPrivilageException(['Admin privilage not found!']);
        }
    }

}
