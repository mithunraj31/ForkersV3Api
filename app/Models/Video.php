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

    public function setEventIdAttribute($id)
    {
        $this->attributes['event_id'] = $id;
    }

    public function setUrlAttribute($url)
    {
        $this->attributes['url'] = $url;
    }

    public function setUserNameAttribute($userName)
    {
        $this->attributes['username'] = $userName;
    }

    public function setDeviceIdAttribute($id)
    {
        $this->attributes['device_id'] = $id;
    }
}
