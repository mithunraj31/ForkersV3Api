<?php

namespace App\Services;

use App\Models\Camera;
use App\Models\DTOs\CameraDto;
use App\Services\Interfaces\CameraServiceInterface;
use Exception;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CameraService implements CameraServiceInterface
{
    public function create(Camera $model)
    {
        $model->save();
    }

    public function update(Camera $model)
    {
        $model->update();
        return response(['message' => 'Success!'], 200);
    }

    public function findById(Camera $model)
    {
        return $model;
    }


    public function findByDeviceId($deviceId)
    {
        $cameras =  Camera::where('device_id', '=', $deviceId)->get();
        if ($cameras->count() == 0) {
            throw new NotFoundResourceException();
        }
        return $cameras;
    }

    public function delete(Camera $model)
    {
        $model->delete();
        return response(['message' => 'Success!'], 200);
    }
}
