<?php

namespace App\AuthValidators;

use App\Enum\AccessType;
use App\Enum\ResourceType;
use App\Exceptions\NoPrivilageException;
use Illuminate\Support\Facades\Auth;

class GroupValidator {
    static function view()
    {   //Check whether the user is admin
        if(UserValidator::isAdmin())return;

        //Check whether user has the required privileges
        $token=json_decode(Auth::token());
        $privileges = $token->privileges;
        $access = false;
        for($i=0; count($privileges)>$i; $i++){
           $p = explode(":",$privileges[$i]);
           if($p[0]==ResourceType::Group && str_contains($p[1],AccessType::View)){
            $access = true;
           break;
           }
        }
        // When user doesnt have the required access return exception
        if(!$access)throw new NoPrivilageException(["No Group View Privilege!"]);
    }

}
