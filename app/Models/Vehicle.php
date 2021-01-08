<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed plate_number
 * @property mixed customer_id
 * @property mixed vehicle_device_id
 * @property mixed customer
 * @property mixed owner
 * @property mixed owner_id
 * @property mixed group_id
 */

class Vehicle extends Model
{
    protected $table = 'vehicle';

    use SoftDeletes;

    protected $fillable = [
        'name',
        'plate_number',
        'customer_id',
        'owner_id',
        'group_id',
        'vehicle_number',
        'structural_method',
        'power_type',
        'rated_load',
        'fork_length',
        'standard_lift',
        'maximum_lift',
        'battery_voltage',
        'battery_capacity',
        'hour_meter_initial_value',
        'operating_time',
        'cumulative_uptime',
        'introduction_date',
        'contract',
        'key_number',
        'installation_location',
        'option1',
        'option2',
        'option3',
        'option4',
        'option5',
        'remarks',
        'model_id'
    ];

    public function VehicleDevices()
    {
        return $this->hasMany('App\Models\VehicleDevice', 'vehicle_id');
    }

    public function Device()
    {
        return $this->hasOne('App\Models\VehicleDevice', 'vehicle_id')->with('device')->latest();
    }

    public function Customer()
    {
        return $this->belongsTo("App\Models\Customer", "customer_id");
    }

    public function Owner()
    {
        return $this->belongsTo("App\Models\User", "owner_id");
    }

    public function Group()
    {
        return $this->belongsTo("App\Models\Group", "group_id");
    }

    public function Model()
    {
        return $this->belongsTo(VehicleModel::class. 'model_id');
    }
}
