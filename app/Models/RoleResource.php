<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleResource extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_resource';
    use SoftDeletes;
    protected $fillable = [
        'resource',
        'add',
        'edit',
        'delete',
        'view',
        'owner_id'
    ];


    public function Customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function Owner()
    {
        return $this->belongsTo('App\Models\User', "owner_id");
    }

    public function Role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }
}
