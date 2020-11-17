<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoConverted extends Model
{
    use HasFactory;

    protected $table = 'video_converted';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'url'
    ];

    public function setUrlAttribute($url)
    {
        $this->attributes['url'] = $url;
    }

    public function setIdAttribute($id)
    {
        $this->attributes['id'] = $id;
    }
}
