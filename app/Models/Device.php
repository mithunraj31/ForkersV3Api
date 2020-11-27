<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
}
