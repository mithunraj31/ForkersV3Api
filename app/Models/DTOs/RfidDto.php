<?php


namespace App\Models\DTOs;

class RfidDto extends DtoBase
{
    public $id;

    public $createdBy;

    public $customerId;

    public $ownerId;

    public $unAssigned;

    public $assigned;

    public $perPage;
}
