<?php

namespace App\AuthValidators;

use App\Enum\SysRole;
use Illuminate\Support\Facades\Auth;

class AuthValidator {
    static function isPrivileged($resourceType, $accessType)
    {
        $token=json_decode(Auth::token());
        $privileges = $token->privileges;
        $access = false;
        for($i=0; count($privileges)>$i; $i++){
           $p = explode(":",$privileges[$i]);
           if($p[0]==$resourceType && str_contains($p[1],$accessType)){
            $access = true;
           break;
           }
        }
        return $access;
    }

    static function isAdmin()
    {
        $token=json_decode(Auth::token());
        if(SysRole::Admin ==$token->sys_role){
            return true;
        } else {
            return false;
        }
    }
    static function getStkUser(){
        $token=json_decode(Auth::token());
        return $token->stk_user;
    }

    static function getGroups(){
        $token=json_decode(Auth::token());
        return $token->groups;
    }

}


