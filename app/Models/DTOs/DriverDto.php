<?php


namespace App\Models\DTOs;

class DriverDto extends DtoBase
{
    public $driverId;

    public $name;

    public $dob;

    public $address;

    public $licenseNo;

    public $licenseReceivedDate;

    public $licenseRenewalDate;

    public $licenseLocation;

    public $phoneNumber;
}