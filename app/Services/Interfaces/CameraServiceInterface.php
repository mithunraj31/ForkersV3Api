<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\CameraDto;

interface CameraServiceInterface
{
    public function create(CameraDto $model);

    public function update(CameraDto $model);

    public function findById($cameraId);

    public function findByDeviceId($deviceId);

    public function delete($cameraId);
}
