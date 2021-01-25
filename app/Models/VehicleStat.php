<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VehicleStat extends Model
{

    protected $table = 'vehicle_stat';

    protected $fillable = [
        'vehicle_id',
        'date',
        'duration'
    ];


    public function Vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

}
