<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeVideoWaitingQueue extends Model
{
    use HasFactory;

    protected $table = 'make_video_waiting_queue';

    protected $fillable = ['*'];

    public function getDeviceIdAttribute()
    {
        return (int) $this->attributes['device_id'];
    }

    public function getBeginDatetimeAttribute()
    {
        return Carbon::parse($this->attributes['begin_datetime']);
    }

    public function getEndDatetimeAttribute()
    {
        return Carbon::parse($this->attributes['end_datetime']);
    }

    public function getUsername()
    {
        return $this->attributes['username'];
    }
}
