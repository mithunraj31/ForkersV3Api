<?php


namespace App\Models\DTOs;

class UserDto extends DtoBase
{
    public $id;

    public $first_name;

    public $last_name;

    public $username;

    public $customer_id;

    public $role_id;

    public $sys_role;

    public $password;

    public $groups;

    public $privileges;
}
