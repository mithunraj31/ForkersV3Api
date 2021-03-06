<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed customer_id
 * @property mixed name
 * @property mixed description
 * @property mixed parent_id
 * @property mixed owner_id
 */
class Group extends Model
{
    protected $table = 'group';
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'customer_id',
        'parent_id'
    ];

    public function children()
    {
       return $this->hasMany('App\Models\Group','parent_id')->with('children');
    }
    public function parent()
    {
       return $this->belongsTo('App\Models\Group','parent_id');
    }
    public function owner()
    {
       return $this->belongsTo('App\Models\User','owner_id');
    }
    public function customer()
    {
       return $this->belongsTo('App\Models\Customer','customer_id');
    }
    public function users()
    {
        return $this->belongsToMany('App\Models\User','user_group','group_id', 'user_id');
    }
    public function vehicles()
    {
        return $this->hasMany('App\Models\Vehicle','group_id')->with('device');
    }
    public function vehicle_ids(){
        return $this->hasMany('App\Models\Vehicle','group_id')->select(['id','group_id']);
    }
}
