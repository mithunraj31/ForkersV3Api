<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\ChartsDto;

interface ChartServiceInterface
{
    public function create(ChartsDto $model);

    public function update(ChartsDto $model);

    public function findById($chartId);

    public function findAll($userId);

    public function delete($chartId);
}
