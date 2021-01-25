<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customer';
    protected $fillable = [
        'id',
        'name',
        'description',
        'stk_user',
    ];

    public function Owner()
    {
        return $this->belongsTo('App\Models\User','owner_id');
    }

    public function Users()
    {
        return $this->hasMany('App\Models\User', 'customer_id');
    }

    public function Roles()
    {
        return $this->hasMany('App\Models\Role','customer_id');
    }

}
