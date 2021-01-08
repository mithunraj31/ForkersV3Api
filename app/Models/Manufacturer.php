<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

Class Manufacturer extends Model
{

    protected $table = 'manufacturer';

    protected $fillable = [
    'owner_id',
    'name',
    'description',
];

    public function Owner()
{
    return $this->belongsTo(User::class, "owner_id");
}

}
