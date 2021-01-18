<?php

namespace App\Services\Interfaces;

use App\Models\Device;
use App\Models\DTOs\DeviceDto;

interface DeviceServiceInterface
{
    public function create(DeviceDto $model);

    public function update(DeviceDto $request,Device $device);

    public function findById(Device $device);

    public function getAll($perPage);

    public function delete(Device $device);

}
