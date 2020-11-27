<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoConverted extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
