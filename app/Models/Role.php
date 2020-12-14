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
        return $this->hasMany('App\Models\role');
    }

    public function Customer()
    {
        return $this->belongsTo("App\Models\Customer", "customer_id");
    }

    public function Owner()
    {
        return $this->belongsTo("App\Models\User", "owner_id");
    }

    public function Privileges()
    {
        return $this->hasMany("App\Models\RoleResource","role_id");
    }
}
