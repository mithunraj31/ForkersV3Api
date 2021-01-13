<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Location extends Model
{
    protected $table = 'location';
    protected $primaryKey = 'vehicle_id';

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class ,'vehicle_id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class ,'operator_id');
    }

}
