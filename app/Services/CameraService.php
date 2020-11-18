<?php

namespace App\Services;

use App\Models\Camera;
use App\Models\DTOs\CameraDto;
use App\Services\Interfaces\CameraServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CameraService implements CameraServiceInterface
{
    public function create(CameraDto $model)
    {
        Log::info('Creating camera', $model->toArray());
        $camera = new Camera();
        $camera->device_id = $model->deviceId;
        $camera->rotation = $model->rotation;
        $camera->ch = $model->ch;
        $camera->save();
        Log::info('Camera has been created');
    }

    public function update(CameraDto $model)
    {
        $camera = $this->findById($model->id);
        $camera->device_id = $model->deviceId;
        $camera->rotation = $model->rotation;
        $camera->ch = $model->ch;
        $camera->update();
    }

    public function findById($cameraId)
    {
        $camera =  Camera::find($cameraId);
        if ($camera == null) {
            throw new NotFoundResourceException();
        }
        return $camera;
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
        $camera = $this->findById($cameraId);
        $camera->delete();
    }
}
