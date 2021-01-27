<?php

namespace App\Services\Interfaces;

use App\Models\Device;
use App\Models\DTOs\DeviceDto;

interface DeviceServiceInterface
{

    /**
     * the method give device listings,
     * each device item contains device's details.
     */
    public function create(DeviceDto $device);

    public function update(DeviceDto $request,Device $device);

    public function findById(Device $device);

    public function getAll($perPage);

    public function delete(Device $device);

}
