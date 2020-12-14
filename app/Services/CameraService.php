<?php

namespace App\Services;

use App\Models\Camera;
use App\Models\DTOs\CameraDto;
use App\Services\Interfaces\CameraServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CameraService extends ServiceBase implements CameraServiceInterface
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
        Log::info('Updating camera', $model->toArray());
        $camera = $this->findById($model->id);
        $camera->device_id = $model->deviceId;
        $camera->rotation = $model->rotation;
        $camera->ch = $model->ch;
        $camera->update();
        Log::info('Camera has been updated');
    }

    public function findById($cameraId)
    {
        $camera =  Camera::find($cameraId);
        if ($camera == null) {
            Log::warning("Not found camera by ID $cameraId");
            throw new NotFoundResourceException();
        }
        return $camera;
    }


    public function findByDeviceId($deviceId)
    {
        $cameras =  Camera::where('device_id', '=', $deviceId)->get();
        if ($cameras->count() == 0) {
            Log::warning("Not found camera for device by ID $deviceId");
            throw new NotFoundResourceException();
        }
        return $cameras;
    }

    public function delete($cameraId)
    {
        $camera = $this->findById($cameraId);
        Log::info('Deleting camera data', (array)  $camera);
        $camera->delete();
        Log::info("Deleted camera by ID $cameraId");
    }
}
