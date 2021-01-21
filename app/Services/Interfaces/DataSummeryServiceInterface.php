<?php

namespace App\Services\Interfaces;

interface DataSummeryServiceInterface
{
    public function getEventsByOperators($start,$end, $operators, $customer=null);

}
