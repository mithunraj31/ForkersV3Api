<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\RfidDto;
use App\Models\DTOs\RfidHistoryDto;

interface RfidServiceInterface
{
    public function create(RfidDto $model);

    public function update(RfidDto $model);

    public function findById($rfid);

    public function findAll(RfidDto $queryBuilder);

    public function delete($rfid);

    public function assignOperator(RfidHistoryDto $model);

    public function removeOperator($rfid, $operatorId);

    public function findrfIdHistory($rfid);
}
