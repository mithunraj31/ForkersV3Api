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
        'operator_id',
        'assigned_from',
        'assigned_till',


    ];
}
