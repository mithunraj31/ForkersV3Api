<?php

namespace App\Services;

use App\Models\DTOs\OperatorDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\Operator;
use App\Models\RfidHistory;
use App\Services\Interfaces\OperatorServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\AlreadyUsedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class OperatorService extends ServiceBase implements OperatorServiceInterface
{
    public function create(OperatorDto $model)
    {
        Log::info('Creating Operator', $model->toArray());
        $operator = new Operator();
        $operator->name = $model->name;
        $operator->dob = $model->dob;
        $operator->address = $model->address;
        $operator->license_no = $model->licenseNo;
        $operator->license_received_date = $model->licenseReceivedDate;
        $operator->license_renewal_date = $model->licenseRenewalDate;
        $operator->license_location = $model->licenseLocation;
        $operator->phone_no = $model->phoneNo;
        $operator->owner_id = $model->ownerId;
        $operator->customer_id = $model->customerId;
        $operator->save();
        Log::info('Operator has been created');
    }

    public function update(OperatorDto $model)
    {
        Log::info('Updating Operator', $model->toArray());
        $operator = $this->findById($model->id);
        $operator->name = $model->name;
        $operator->dob = $model->dob;
        $operator->address = $model->address;
        $operator->license_no = $model->licenseNo;
        $operator->license_received_date = $model->licenseReceivedDate;
        $operator->license_renewal_date = $model->licenseRenewalDate;
        $operator->license_location = $model->licenseLocation;
        $operator->phone_no = $model->phoneNo;
        $operator->owner_id = $model->ownerId;
        $operator->customer_id = $model->customerId;
        $operator->update();
        Log::info('Operator has been updated');
    }

    public function findById($id)
    {
        $operator =  Operator::find($id);
        if ($operator == null) {
            Log::warning("Not found Operator by ID $id");
            throw new NotFoundResourceException();
        }
        return $operator;
    }


    public function findAll(OperatorDto $queryBuilder)
    {
        $operatorsData = [];
        if (($queryBuilder->unAssigned && $queryBuilder->assigned) ||
            (!$queryBuilder->unAssigned && !$queryBuilder->assigned)
        ) {
            $query =  Operator::with('rfid');

            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }

            $operatorsData = $query->get();
            $operatorsData->transform(function ($value) {
                $model = $value->toArray();
                $model['rfid'] = $model['rfid'] != null && $model['rfid']['assigned_till'] == null
                    ? $model['rfid']['rfid']['id'] : null;
                return $model;
            });
        } else if (!$queryBuilder->unAssigned && $queryBuilder->assigned) {
            $query = Operator::with('rfid')->has('rfid');

            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }

            $operatorsData = $query->get();
            $operatorsData->transform(function ($value) {
                $model = $value->toArray();
                $model['rfid'] = $model['rfid'] != null && $model['rfid']['assigned_till'] == null
                    ? $model['rfid']['rfid']['id'] : null;
                return $model;
            });
        } else if ($queryBuilder->unAssigned && !$queryBuilder->assigned) {
            $query = Operator::with('rfid')->doesntHave('rfid');

            if ($queryBuilder->customerId) {
                $query->where('customer_id', $queryBuilder->customerId);
            }

            $operatorsData = $query->get();
            $operatorsData->transform(function ($value) {
                $model = $value->toArray();
                $model['rfid'] = $model['rfid'] != null && $model['rfid']['assigned_till'] == null
                    ? $model['rfid']['rfid']['id'] : null;
                return $model;
            });
        }
        if ($queryBuilder->perPage) {
            $result = CollectionUtility::paginate($operatorsData, $queryBuilder->perPage);
            return  $result;
        }
        return $operatorsData;
    }


    public function delete($operatorId)
    {
        $operator = $this->findById($operatorId);
        Log::info('Deleting Operator data', (array)  $operator);
        $operator->delete();
        Log::info("Deleted Operator by ID $operatorId");
    }

    public function assignRfid(RfidHistoryDto $model)
    {
        Log::info('Assigning Rfid ', $model->toArray());
        $rfidHistory = new RfidHistory();
        $rfidHistory->rfid = $model->rfid;
        $rfidHistory->operator_id = $model->operatorId;
        $rfidHistory->assigned_from = $model->assignedFrom;
        $rfidHistory->assigned_till = $model->assignedTill;
        $rfidHistory->save();
        Log::info('Rfid has been assigned');
    }

    public function removeRfid($operatorId, $rfid)
    {
        Log::info('Removing rfid for Operator ');
        $rfidHistory = $this->findCurrentAssignedRfid($operatorId, $rfid);
        if ($rfidHistory->assigned_till != null) {
            throw new AlreadyUsedException();
        }
        $rfidHistory->assigned_till = Carbon::now();
        $rfidHistory->update();
        Log::info('Rfid Removed for Operator');
    }

    public function findCurrentAssignedRfid($id, $rfid)
    {
        $rfids =  RfidHistory::where([
            ['operator_id', '=', $id],
            ['rfid', '=', $rfid],
            ['assigned_till', '=', null]
        ])->first();
        if ($rfids == null) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }
}
