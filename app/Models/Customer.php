<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    protected $table = 'customer';
    use SoftDeletes;

    public function owner()
    {
        return $this->belongsTo('App\Models\User','owner_id');
    }
}
