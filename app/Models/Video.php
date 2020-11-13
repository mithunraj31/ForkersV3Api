<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'video';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'url',
        'username',
        'device_id',
    ];
}
