<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'driver';


    protected $fillable = [
        'driver_id',
        'name',
        'dob',
        'address',
        'license_no',
        'license_received_date',
        'license_renewal_date',
        'license_location',
        'phone_no'
    ];
}
