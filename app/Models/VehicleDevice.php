<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed vehicle_id
 * @property mixed device_id
 * @property mixed owner_id
 * @property mixed vehicle
 * @property mixed owner
 * @property mixed device
 */
Class VehicleDevice extends Model
{

    protected $table = 'vehicle_device';
    use SoftDeletes;
    protected $fillable = [
    'vehicle_id',
    'device_id',
    'owner_id'
];


    public function Vehicle()
{
    return $this->belongsTo('App\Models\Vehicle', 'vehicle_id');
}

    public function Owner()
{
    return $this->belongsTo('App\Models\User', "owner_id");
}

    public function Device()
{
    return $this->belongsTo('App\Models\Device','device_id');
}
}
