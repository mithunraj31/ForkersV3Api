<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charts extends Model
{
    use HasFactory;

    protected $table = 'charts';

    protected $fillable = [
        'id',
        'name',
        'type',
        'api_path',
        'owner_id',
        'customer_id',
        'is_private'
    ];
}
