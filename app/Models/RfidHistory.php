<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidHistory extends Model
{
    use HasFactory;

    protected $table = 'rfid_history';


    protected $fillable = [
        'rfid',
        'operator_id',
        'assigned_from',
        'assigned_till',


    ];

    public function Rfid()
    {
        return $this->belongsTo("App\Models\Rfid", "rfid");
    }

    public function Operator()
    {
        return $this->belongsTo("App\Models\Operator", "operator_id");
    }
}
