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
        'customer_id',
        'owner_id',
        'group_id'
    ];

    public function Operator()
    {
        return $this->hasOne('App\Models\RfidHistory', 'rfid')->with('operator')->latest();
    }

    public function Customer()
    {
        return $this->belongsTo("App\Models\Customer", "customer_id");
    }

    public function Owner()
    {
        return $this->belongsTo("App\Models\User", "owner_id");
    }

    public function Group()
    {
        return $this->belongsTo("App\Models\Group", "group_id");
    }
}
