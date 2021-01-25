<?php


namespace App\Models\DTOs;

class ChartsDto extends DtoBase
{
    public $id;

    public $name;

    public $type;

    public $apiPath;

    public $isPrivate;

    public $ownerId;

    public $customerId;
}
