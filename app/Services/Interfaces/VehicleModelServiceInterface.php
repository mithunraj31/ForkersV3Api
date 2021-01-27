<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\VehicleModelDto;

interface VehicleModelServiceInterface
{
    public function create(VehicleModelDto $vehicleModel);

    public function update(VehicleModelDto $vehicleModel);

    public function findById($vehicleModelId);

    public function getAll();

    public function delete($vehicleModelId);
}
