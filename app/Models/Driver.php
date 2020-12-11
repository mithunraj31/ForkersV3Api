<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'driver';

    protected $primaryKey = 'driver_id';

    protected $fillable = [
        'driver_id',
        'driver_name',
        'driver_status',
        'driver_license_no',
    ];
}
