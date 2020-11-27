<?php

namespace App\Models;

use BaoPham\DynamoDb\DynamoDbModel;

class Regular extends DynamoDbModel
{
    protected $table = 'forkers_rd_location_dev';
}
