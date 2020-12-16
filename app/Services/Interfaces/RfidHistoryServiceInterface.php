<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\RfidHistoryDto;

interface RfidHistoryServiceInterface
{
    public function assignOperator(RfidHistoryDto $model);

    public function removeOperator($rfid);

    public function findrfIdHistory($rfid);

    public function findAll();

    public function delete($id);
}
