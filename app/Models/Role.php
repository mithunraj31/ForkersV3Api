<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed description
 * @property mixed customer_id
 * @property mixed users
 * @property mixed customers
 * @property mixed owner
 * @property mixed privileges
 * @property mixed owner_id
 */

class Role extends Model
{
    protected $table = 'role';

    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'customer_id',
    ];

    public function Users()
    {
        return $this->hasMany('App\Models\User', 'role_id');
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
