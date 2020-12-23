<?php

namespace App\Services\Interfaces;

use App\Models\Device;
use App\Models\DTOs\DeviceDto;

interface DeviceServiceInterface
{
<<<<<<< Updated upstream
    /**
     * the method give device listings,
     * each device item contains device's details.
     */
    public function getAllDevice();
=======
    public function create(DeviceDto $device);

    public function update(DeviceDto $request,Device $device);

    public function findById(Device $device);
>>>>>>> Stashed changes

    public function getAll();

    public function delete(Device $device);

}
