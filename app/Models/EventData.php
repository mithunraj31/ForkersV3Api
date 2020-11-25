<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventData extends Model
{

    use HasFactory;

    protected $table = 'event';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'event_id',
        'device_id',
        'driver_id',
        'type',
        'latitude',
        'longitude',
        'gx',
        'gy',
        'gz',
        'roll',
        'pitch',
        'yaw',
        'status',
        'direction',
        'speed',
        'video_id',
        'time',
        'username',
    ];
}
