<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\DriverDto;

interface DriverServiceInterface
{
    public function create(DriverDto $model);

    public function update(DriverDto $model);

    public function findById($id);

    public function findAll();

    public function delete($id);

    public function findRegularDataByDriverId($driverId);
}
