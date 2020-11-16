<?php

namespace App\Services;

use App\Models\Camera;
use App\Models\DTOs\CameraDto;
use App\Services\Interfaces\CameraServiceInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CameraService implements CameraServiceInterface
{
    public function create(CameraDto $model)
    {
        $camera = new Camera();
        $camera->device_id = $model->deviceId;
        $camera->rotation = $model->rotation;
        $camera->ch = $model->ch;
        $camera->save();
    }

    public function update(CameraDto $model)
    {
        $camera = Camera::find($model->id);
        $camera->device_id = $model->deviceId;
        $camera->rotation = $model->rotation;
        $camera->ch = $model->ch;
        $camera->update();
    }

    public function findById($cameraId)
    {
        $cameras =  Camera::where('id', '=', $cameraId)->get();
        if ($cameras->count() == 0) {
            throw new NotFoundResourceException();
        }
        return $cameras;
    }


    public function findByDeviceId($deviceId)
    {
        $cameras =  Camera::where('device_id', '=', $deviceId)->get();
        if ($cameras->count() == 0) {
            throw new NotFoundResourceException();
        }
        return $cameras;
    }

    public function delete($cameraId)
    {
        Camera::where('id', '=', $cameraId)->delete();
    }
}
