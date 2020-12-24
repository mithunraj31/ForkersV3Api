<?php


namespace App\Models\DTOs;

class OperatorDto extends DtoBase
{
    public $id;

    public $name;

    public $dob;

    public $address;

    public $licenseNo;

    public $licenseReceivedDate;

    public $licenseRenewalDate;

    public $licenseLocation;

    public $phoneNumber;

    public $unAssigned;

    public $assigned;

    public $perPage;
}
