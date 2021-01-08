<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Device_backup extends Model
{
    use SoftDeletes;

    protected $table = 'device';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plate_number',
        'scan_code',
        'channel_number',
        'group_name',
        'device_type',
        'is_active',
        'stk_user',
        'stk_device'
    ];

    public function scopeGetLatestDevice($builder, $stkUser = null)
    {
        $optionalCondition = '';
        if($stkUser != null) {
            $optionalCondition = "WHERE stk_user = '$stkUser'";
        }

        // Look SQL query at /database/migrations/2020_12_09_040748_create_latest_devices_view.php
        $devices = DB::select("select * from latest_deivces $optionalCondition");
        return $devices;
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
}
