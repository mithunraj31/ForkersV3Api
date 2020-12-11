<?php

namespace App\Services;

use App\Models\DTOs\RfidHistoryDto;
use App\Models\RfidHistory;
use App\Services\Interfaces\RfidHistoryServiceInterface;
use Illuminate\Support\Facades\Log;

class RfidHistoryService extends ServiceBase implements RfidHistoryServiceInterface
{
    public function create(RfidHistoryDto $model)
    {
        Log::info('Creating Rfid History', $model->toArray());
        $rfid = new RfidHistory();
        $rfid->rfid = $model->rfid;
        $rfid->driver_id = $model->driverId;
        $rfid->begin_time = $model->beginTime;
        $rfid->end_time = $model->endTime;
        $rfid->save();
        Log::info('Rfid has been created');
    }
}
