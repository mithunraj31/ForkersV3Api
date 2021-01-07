<?php

namespace App\Services;


use App\Models\Rfid;
use App\Models\DTOs\RfidDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\RfidHistory;
use App\Services\Interfaces\RfidServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $rfid->save();
        Log::info('Rfid has been created');
    }

    public function update(RfidDto $model)
    {
        Log::info('Updating Rfid', $model->toArray());
        $rfid = $this->findById($model->id);
        $rfid->customer_id = $model->customerId;
        $rfid->owner_id = $model->ownerId;
        $rfid->update();
        Log::info('Rfid has been updated');
    }

    public function findById($id)
    {
        $rfid =  Rfid::with('operator')->find($id);
        if ($rfid == null) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfid;
    }


    public function findAll(RfidDto $queryBuilder)
    {
        $rfidData = [];
        // get data without considering assigned or unasigned
        if (($queryBuilder->unAssigned && $queryBuilder->assigned) ||
            (!$queryBuilder->unAssigned && !$queryBuilder->assigned)
        ) {
            $query =  Rfid::with('operator', 'customer');
            // query by customer
            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }
            $rfidData = $query->get();
            $rfidData->transform(function ($value) {
                $model = $value->toArray();
                $model['operator_id'] = $model['operator'] != null ? $model['operator']['operator']['id'] : null;
                $model['operator_name'] = $model['operator'] != null ? $model['operator']['operator']['name'] : null;
                return $model;
            });
        } // when query is only to get assigned rfids
        else if (!$queryBuilder->unAssigned && $queryBuilder->assigned) {
            $query = Rfid::with('operator', 'customer')->has('Operator');
            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }
            $rfidData = $query->get();
            $rfidData->transform(function ($value) {
                $model = $value->toArray();
                $model['operator_id'] = $model['operator'] != null ? $model['operator']['operator']['id'] : null;
                $model['operator_name'] = $model['operator'] != null ? $model['operator']['operator']['name'] : null;
                return $model;
            });
        } // when query is only to get unassigned rfids
        else if ($queryBuilder->unAssigned && !$queryBuilder->assigned) {
            $query = Rfid::with('operator', 'customer')->doesntHave('Operator');
            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }
            $rfidData = $query->get();
            $rfidData->transform(function ($value) {
                $model = $value->toArray();
                $model['operator_id'] = $model['operator'] != null ? $model['operator']['operator']['id'] : null;
                $model['operator_name'] = $model['operator'] != null ? $model['operator']['operator']['name'] : null;
                return $model;
            });
        } // when pagination is needed
        if ($queryBuilder->perPage) {
            $result = CollectionUtility::paginate($rfidData, $queryBuilder->perPage);
            return  $result;
        }
        return $rfidData;
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
        $rfidHistory->save();
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

    public function removeOperator($rfid, $operatorId)
    {
        Log::info('Removing operator for Rfid ');
        $rfidHistory = $this->findCurrentAssignedOperator($rfid, $operatorId);
        if ($rfidHistory->assigned_till != null) {
            throw new AlreadyUsedException();
        }
        $rfidHistory->assigned_till = Carbon::now();
        $rfidHistory->update();
        Log::info('Operator Removed for Rfid');
    }

    public function findCurrentAssignedOperator($rfid, $operatorId)
    {
        $rfids =  RfidHistory::where([
            ['rfid', $rfid],
            ['operator_id', $operatorId],
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
        $rfids =  RfidHistory::with('operator')->where('rfid', $rfid)
            ->orderBy('assigned_till')
            ->get();
        if ($rfids == null) {
            Log::warning("Not found Rfid by ID $rfids");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }
}
