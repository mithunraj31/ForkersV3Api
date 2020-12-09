<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\DriverDto;

interface DriverServiceInterface
{
    public function create(DriverDto $model);

    public function update(DriverDto $model);

    public function findById($driverId);

    public function findAll();

    public function delete($driverId);
}
