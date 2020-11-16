<?php

namespace App\Services\Interfaces;

use App\Models\Camera;
use App\Models\DTOs\CameraDto;

interface CameraServiceInterface
{
    public function create(Camera $model);

    public function update(Camera $model);

    public function findById(Camera $model);

    public function findByDeviceId(Camera $model);

    public function delete(Camera $model);
}
