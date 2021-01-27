<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed type
 * @property mixed latitude
 * @property mixed longitude
 * @property mixed driver_id
 */
class Location extends Model
{
    protected $table = 'location';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'type',
        'latitude',
        'longitude',
        'time',
        'driver_id'
    ];

}
