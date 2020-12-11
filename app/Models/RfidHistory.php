<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidHistory extends Model
{
    use HasFactory;

    protected $table = 'rfid_history';

    public $timestamps = false;

    protected $fillable = [
        'rfid',
        'driver_id',
        'begin_time',
        'end_time'
    ];
}
