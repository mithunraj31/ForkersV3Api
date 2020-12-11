<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\RfidHistoryDto;

interface RfidHistoryServiceInterface
{
    public function create(RfidHistoryDto $model);
}
