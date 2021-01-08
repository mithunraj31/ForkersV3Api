<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\OperatorDto;
use App\Models\DTOs\RfidHistoryDto;

interface OperatorServiceInterface
{
    public function create(OperatorDto $model);

    public function update(OperatorDto $model);

    public function findById($operatorId);

    public function findAll(OperatorDto $queryBuilder);

    public function delete($operatorId);

    public function assignRfid(RfidHistoryDto $model);

    public function removeRfid($operatorId, $rfid);
}
