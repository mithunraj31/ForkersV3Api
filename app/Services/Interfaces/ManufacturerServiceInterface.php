<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\ManufacturerDto;

interface ManufacturerServiceInterface
{
    public function create(ManufacturerDto $model);

    public function update(ManufacturerDto $model);

    public function findById($id);

    public function getAll(ManufacturerDto $model = null);

    public function delete($id);

}
