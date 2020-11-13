<?php

namespace App\Services\Interfaces;


interface CameraServiceInterface
{
    public function create($model);

    public function update($model);

    public function findById($id);

    public function findAll();

    public function findByDeviceId($id);

    public function delete($id);
}
