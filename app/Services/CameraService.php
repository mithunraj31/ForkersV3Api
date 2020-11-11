<?php

namespace App\Services;

use App\Models\Camera;
use App\Services\Interfaces\CameraServiceInterface;
use Exception;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CameraService implements CameraServiceInterface
{
    public function create($model)
    {
        throw new Exception('Not implemented');
    }

    public function update($model)
    {
        throw new Exception('Not implemented');
    }

    public function findById($id)
    {
        throw new Exception('Not implemented');
    }

    public function findAll()
    {
        throw new Exception('Not implemented');
    }

    public function findByDeviceId($deviceId)
    {
        $cameras =  Camera::where('device_id', '=', $deviceId)->get();
        if ($cameras->count() == 0) {
            throw new NotFoundResourceException();
        }
        return $cameras;
    }

    public function delete($id)
    {
        throw new Exception('Not implemented');
    }
}
