<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $table = 'operator';

    protected $fillable = [
        'id',
        'name',
        'dob',
        'address',
        'license_no',
        'license_received_date',
        'license_renewal_date',
        'license_location',
        'phone_no'
    ];


    public function rfid()
    {
        return $this->hasOne('App\Models\RfidHistory', 'operator_id')->with('rfid')->latest();
    }

    public function rfidHistory()
    {
        return $this->hasMany('App\Models\RfidHistory', 'operator_id');
    }
}
