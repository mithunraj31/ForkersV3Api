<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfid extends Model
{
    use HasFactory;

    protected $table = 'rfid';

    protected $fillable = [
        'id',
        'rfid',
        'rfid_name',
        'created_by',
    ];

    public function history()
    {
        return $this->belongsTo('App\Models\RfidHistory', 'rfid');
    }
}
