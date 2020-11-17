<?php

namespace App\Services\Interfaces;

interface OperatorServiceInterface
{
    /**
     * the method give operator drive summery listings,
     * each device item contains device's details.
     * @return array
     */
    public function getDriveSummery($operatorId, $startTime, $endTime);

}
