<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OperatorStat extends Model
{

    protected $table = 'operator_stat';

    protected $fillable = [
        'operator_id',
        'date',
        'duration'
    ];


    public function Operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }

}
