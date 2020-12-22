<?php
namespace App\Models\DTOs;

class GroupDto extends DtoBase
{
    public $id;

    public $name;

    public $description;

    public $parent_id;

    public $owner_id;

    public $customer_id;
}
