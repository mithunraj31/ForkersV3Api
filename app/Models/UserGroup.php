<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_group';

    public function group()
    {
        return $this->belongsTo('App\Group','group_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    public function owner()
    {
        return $this->belongsTo('App\User','owner_id');
    }
}
