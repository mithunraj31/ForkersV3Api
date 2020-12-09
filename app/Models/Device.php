<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'plate_number',
        'scan_code',
        'channel_number',
        'group_name',
        'device_type',
        'is_active',
        'stk_user',
        'stk_device'
    ];

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
}
