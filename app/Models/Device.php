<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'device_id';

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
        'tcp_server_addr',
        'tcp_stream_out_port',
        'udp_server_addr',
        'udp_stream_out_port',
        'net_type',
        'device_type',
        'create_time',
        'update_time',
        'is_active',
        'stk_user',
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
