<?php


namespace App\Models\DTOs;

class RoleDto extends DtoBase
{
    public $id;

    public $name;

    public $description;

    public $customer_id;

    public $privileges;
}
