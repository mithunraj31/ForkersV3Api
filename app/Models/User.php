<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'customer_id',
        'role_id',
        'owner_id'
    ];
    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function sysRoles()
    {
        return $this->hasMany('App\SysRole');
    }
    public function userGroups()
    {
        return $this->hasMany('App\UserGroup');
    }

}
