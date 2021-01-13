<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\VehicleDto;
use App\Models\Vehicle;

interface VehicleServiceInterface
{
    public function create(VehicleDto $request);

    public function update(VehicleDto $request,Vehicle $vehicle);

    public function findById(Vehicle $vehicle);

    public function getAll();

    public function delete(Vehicle $vehicle);

    public function getDriveSummary($vehicleId, $start, $end);
}
