<?php

namespace App\Services;

use App\Http\Resources\RfidResources;
use App\Models\DTOs\RfidDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\Rfid;
use App\Models\RfidHistory;
use App\Services\Interfaces\RfidServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class RfidService extends ServiceBase implements RfidServiceInterface
{
    public function create(RfidDto $model)
    {
        Log::info('Creating Rfid', $model->toArray());
        $rfid = new Rfid();
        $rfid->rfid = $model->rfid;
        $rfid->rfid_name = $model->rfidName;
        $rfid->created_by = $model->createdBy;

        Log::info('Creating Rfid History');
        foreach ($model->history as $rfidHistory) {
            $rfid = new RfidHistory();
            $rfid->rfid = $model->rfid;
            $rfid->begin_time = $rfidHistory['begin_time'];
            $rfid->end_time = $rfidHistory['end_time'];
            $rfid->driver_id = $rfidHistory['driver_id'];
            $rfid->save();
            Log::info('Rfid has been created');
        }

        $rfid->save();
        Log::info('Rfid has been created');
    }

    public function update(RfidDto $model)
    {
        Log::info('Updating Rfid', $model->toArray());
        $rfid = $this->findById($model->id);
        $rfid->rfid = $model->rfid;
        $rfid->rfid_name = $model->rfidName;
        $rfid->created_by = $model->createdBy;
        $rfid->update();
        Log::info('Rfid has been updated');
        $rfid = $this->deleteHistoryById($model->rfid);
        Log::info('Creating Rfid History');
        foreach ($model->history as $rfidHistory) {
            $rfid = new RfidHistory();
            $rfid->rfid = $model->rfid;
            $rfid->begin_time = $rfidHistory['begin_time'];
            $rfid->end_time = $rfidHistory['end_time'];
            $rfid->driver_id = $rfidHistory['driver_id'];
            $rfid->save();
            Log::info('Rfid has been created');
        }
    }

    public function findById($rfidId)
    {
        $rfid =  Rfid::find($rfidId)->first();
        if ($rfid == null) {
            Log::warning("Not found Rfid by ID $rfidId");
            throw new NotFoundResourceException();
        }
        return  $rfid;
    }


    public function findAll()
    {
        $rfids =  Rfid::all();
        if ($rfids->count() == 0) {
            Log::warning("Not found Rfids");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }

    public function deleteHistoryById($rfid)
    {
        $rfid = RfidHistory::where('rfid', '=', $rfid);
        Log::info('Deleting Rfid data', (array)  $rfid);
        $rfid->delete();
        Log::info("Deleted Rfid by ID $rfid");
    }

    public function delete($rfid)
    {
        $this->deleteHistoryById($rfid);
        $rfid = $this->findById($rfid);
        Log::info('Deleting Rfid data', (array)  $rfid);
        $rfid->delete();
        Log::info("Deleted Rfid by ID $rfid");
    }
}
