<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\RfidDto;

interface RfidServiceInterface
{
    public function create(RfidDto $model);

    public function update(RfidDto $model);

    public function findById($id);

    public function findAll();

    public function delete($id);
}
