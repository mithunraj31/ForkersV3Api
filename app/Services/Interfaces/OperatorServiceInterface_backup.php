<?php

namespace App\Services\Interfaces;

interface OperatorServiceInterface_backup
{
    /**
     * the method give operator drive summery listings,
     * each device item contains device's details.
     * @return array
     */
    public function getDriveSummery($operatorId, $startTime, $endTime);


    public function getOperatorEvents($operatorId, $startTime, $endTime);
}
