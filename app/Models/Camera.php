<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    protected $table = 'camera';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'device_id',
        'rotation',
        'ch'
    ];
}
