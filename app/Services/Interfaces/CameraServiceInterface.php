<?php

namespace App\Services\Interfaces;

use App\Models\Camera;

interface CameraServiceInterface
{
    public function create(Camera $model);

    public function update($request, Camera $model);

    public function findById(Camera $model);

    public function findByDeviceId(Camera $model);

    public function delete(Camera $model);
}
