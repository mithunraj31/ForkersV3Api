<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

Class VehicleModel extends Model
{

    protected $table = 'vehicle_model';

    protected $fillable = [
    'series_name',
    'model_name',
    'power_type',
    'structural_method',
    'engine_model',
    'rated_load',
    'fork_length',
    'fork_width',
    'standard_lift',
    'maximum_lift',
    'battery_voltage',
    'battery_capacity',
    'fuel_tank_capcity',
    'body_weight',
    'body_length',
    'body_width',
    'head_guard_height',
    'min_turning_radius',
    'ref_load_center',
    'tire_size_front_wheel',
    'tire_size_rear_wheel',
    'remarks',
    'owner_id',
    'manufacturer_id'
];

    public function Owner()
{
    return $this->belongsTo('App\Models\User', "owner_id");
}

    public function Manufacturer()
{
    return $this->belongsTo('App\Models\Manufacturer','manufacturer_id');
}
}
