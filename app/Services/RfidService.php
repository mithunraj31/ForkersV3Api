<?php

namespace App\Services;


use App\Models\Rfid;
use App\Models\DTOs\RfidDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\RfidHistory;
use App\Services\Interfaces\RfidServiceInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\AlreadyUsedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class RfidService extends ServiceBase implements RfidServiceInterface
{
    public function create(RfidDto $model)
    {
        Log::info('Creating Rfid', $model->toArray());
        $rfid = new Rfid();
        $rfid->id = $model->id;
        $rfid->customer_id = $model->customerId;
        $rfid->owner_id = $model->ownerId;
        $rfid->group_id = $model->groupId;
        $rfid->save();
        Log::info('Rfid has been created');
    }

    public function update(RfidDto $model)
    {
        Log::info('Updating Rfid', $model->toArray());
        $rfid = $this->findById($model->id);
        $rfid->customer_id = $model->customerId;
        $rfid->owner_id = $model->ownerId;
        $rfid->group_id = $model->groupId;
        $rfid->update();
        Log::info('Rfid has been updated');
    }

    public function findById($id)
    {
        $rfid =  Rfid::find($id);
        if ($rfid == null) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfid;
    }


    public function findAll()
    {
        $Rfids =  Rfid::all();
        if ($Rfids == null) {
            Log::warning("Not found Rfids");
            throw new NotFoundResourceException();
        }
        return $Rfids;
    }

    public function delete($rfid)
    {
        $rfid = $this->findById($rfid);
        Log::info('Deleting Rfid data', (array)  $rfid);
        $rfid->delete();
        Log::info("Deleted Rfid by ID $rfid");
    }

    public function assignOperator(RfidHistoryDto $model)
    {
        Log::info('Creating Rfid History', $model->toArray());
        $rfidHistory = new RfidHistory();
        $rfidHistory->rfid = $model->rfid;
        $rfidHistory->operator_id = $model->operatorId;
        $rfidHistory->assigned_from = $model->assignedFrom;
        $rfidHistory->assigned_till = $model->assignedTill;
        if ($this->checkOperatorIsAlreadyAssigned($model->operatorId)) {
            throw new AlreadyUsedException();
        }
        $rfid = Rfid::where('rfid', $model->rfid)->first();
        if ($rfid == null) {
            Log::warning("Not found rfid for  $model->rfid");
            throw new NotFoundResourceException();
        } else if ($rfid->current_operator_id != 0) {
            throw new AlreadyUsedException();
        }
        $rfid->current_operator_id = $model->operatorId;
        $rfidHistory->save();
        $rfid->update();
        Log::info('Rfid History has been created');
    }

    public function checkOperatorIsAlreadyAssigned($operatorId)
    {
        $opids = RfidHistory::where([
            ['operator_id', $operatorId],
            ['assigned_till', null]
        ])->first();
        if ($opids == null) {
            return false;
        }
        return true;
    }

    public function removeOperator($rfid)
    {
        Log::info('Removing operator for Rfid ');
        $rfidHistory = $this->findCurrentAssignedOperator($rfid);
        if ($rfidHistory->assigned_till != null) {
            throw new AlreadyUsedException();
        }
        $rfidHistory->assigned_till = Carbon::parse(new DateTime());
        $rfidHistory->update();
        $rfid = Rfid::where('rfid', $rfid)->first();
        $rfid->current_operator_id = 0;
        $rfid->update();
        Log::info('Operator Removed for Rfid');
    }

    public function findCurrentAssignedOperator($rfid)
    {
        $rfids =  RfidHistory::where([
            ['rfid', $rfid],
            ['assigned_till', null]
        ])->first();
        if ($rfids === null) {
            Log::warning("Not found Rfid by ID $rfid");
            throw new AlreadyUsedException();
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
}
