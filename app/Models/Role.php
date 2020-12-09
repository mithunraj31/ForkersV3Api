<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'role';

    use SoftDeletes;

    public function Users()
    {
        return $this->hasMany('App\role');
    }

    public function Customer()
    {
        return $this->belongsTo("App\Customer", "customer_id");
    }

    public function Owner()
    {
        return $this->belongsTo("App\User", "owner_id");
    }

    public function Privileges()
    {
        return $this->hasMany("App\RoleResource","role_id");
    }
}
