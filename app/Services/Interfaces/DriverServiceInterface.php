<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\DriverDto;
use App\Models\DTOs\RfidHistoryDto;

interface DriverServiceInterface
{
    public function create(DriverDto $model);

    public function update(DriverDto $model);

    public function findById($id);

    public function findAll();

    public function delete($id);

    public function assignRfid(RfidHistoryDto $model);

    public function removeRfid($operatorId);
}
