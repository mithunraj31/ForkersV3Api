<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\VehicleModelDto;
use App\Models\VehicleModel;

interface VehicleModelServiceInterface
{
    public function create(VehicleModelDto $request);

    public function update(VehicleModelDto $request, VehicleModel $vehicleModel);

    public function findById(VehicleModelDto $vehicle);

    public function getAll();

    public function delete(VehicleModel $vehicleModel);
}
