<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class Device
 * @package App\Models
 *
 * @property mixed id
 * @property mixed customer_id
 * @property mixed stk_user
 * @property mixed owner_id
 * @property mixed channel_number
 * @property mixed plate_number
 * @property mixed assigned
 * @property mixed group_id
 * @property mixed device_type
 */

class Device extends Model
{
    use SoftDeletes;


    protected $table = 'device';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'plate_number',
        'channel_number',
        'group_name',
        'device_type',
        'is_active',
        'stk_user',
        'stk_device'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function location()
    {
        return $this->hasOne(Location::class,'id','id');
    }
    public function vehicle()
    {
       return $this->hasOne(VehicleDevice::class,'device_id')->latest();
    }
}
