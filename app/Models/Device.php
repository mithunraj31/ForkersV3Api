<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'scan_code',
        'channel_number',
        'device_type',
        'is_active',
        'stk_user',
        'customer_id',
        'owner_id',
        'assigned'
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'device_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id');
    }

    public function vehicle()
    {
        return $this->hasOne('App\Models\VehicleDevice', 'device_id')->with('vehicle')->latest();
    }
}
