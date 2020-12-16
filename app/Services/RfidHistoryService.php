<?php

namespace App\Services;


use App\Models\RfidHistory;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\Rfid;
use App\Services\Interfaces\RfidHistoryServiceInterface;
use DateTime;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class RfidHistoryService extends ServiceBase implements RfidHistoryServiceInterface
{
    public function assignOperator(RfidHistoryDto $model)
    {
        Log::info('Creating Rfid History', $model->toArray());
        $rfidHistory = new RfidHistory();
        $rfidHistory->rfid = $model->rfid;
        $rfidHistory->operator_id = $model->operatorId;
        $rfidHistory->assigned_from = $model->assignedFrom;
        $rfidHistory->assigned_till = $model->assignedTill;
        $rfidHistory->save();
        $rfid = Rfid::where('rfid', $model->rfid)->first();
        if ($rfid->count() == 0) {
            Log::warning("Not found rfid for  $model->rfid");
            throw new NotFoundResourceException();
        }
        $rfid->assign_status = 1;
        $rfid->update();
        Log::info('Rfid History has been created');
    }

    public function removeOperator($rfid)
    {
        Log::info('Removing operator for Rfid ');
        $rfidHistory = $this->findCurrentAssignedOperator($rfid);
        $rfidHistory->assigned_till = new DateTime();
        $rfidHistory->update();
        $rfid = Rfid::where('rfid', $rfid)->first();
        $rfid->assign_status = 0;
        $rfid->update();
        Log::info('Operator Removed for Rfid');
    }

    public function findCurrentAssignedOperator($rfid)
    {
        $rfids =  RfidHistory::where([
            ['rfid', $rfid],
            ['assigned_till', null]
        ])->first();
        if ($rfids->count() == 0) {
            Log::warning("Not found Rfid by ID $rfid");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }

    public function findrfIdHistory($rfid)
    {
        $rfids =  RfidHistory::where('rfid', $rfid)
            ->orderBy('assigned_till')
            ->get();
        if ($rfids == null) {
            Log::warning("Not found Rfid by ID $rfids");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }


    public function findAll()
    {
        $rfids =  RfidHistory::all();
        if ($rfids->count() == 0) {
            Log::warning("Not found Rfids");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }

    public function delete($id)
    {
        $rfid = RfidHistory::find($id);
        Log::info('Deleting Rfid History data', (array)  $rfid);
        $rfid->delete();
        Log::info("Deleted Rfid History by ID $id");
    }
}
