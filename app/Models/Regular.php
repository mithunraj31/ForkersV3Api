<?php

namespace App\Models;

use BaoPham\DynamoDb\DynamoDbModel;

class Regular extends DynamoDbModel
{
    protected $table = 'forkers_v3_dev';
    protected $primaryKey = 'vehicle_id';
    protected $compositeKey = ['vehicle_id', 'datetime'];

    protected $dynamoDbIndexKeys = [
        'operator_id-index' => [
            'hash' => 'operator_id',
            'range' => 'datetime'
        ],
    ];
}
