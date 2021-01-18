<?php
namespace App\Models\DTOs;

class ManufacturerDto extends DtoBase
{
    public $id;

    public $name;

    public $description;

    public $customerId;

    public $ownerId;

    public $perPage;

    public $page;
}
