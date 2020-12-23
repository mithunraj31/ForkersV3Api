<?php

namespace App\Services;

use App\Models\DTOs\OperatorDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\Operator;
use App\Models\Rfid;
use App\Models\RfidHistory;
use App\Services\Interfaces\OperatorServiceInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
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
        if ($queryBuilder->unAssigned && $queryBuilder->assigned) {

            // $operatorsData = Operator::with('rfidHistory')->get();
            // $testD = $operatorsData[1]->rfid;
            // if ($operatorsData->assigned_till)


            $operators =  DB::table('operator')
                ->leftJoin('rfid_history', function ($join) {
                    $join->on('operator.id', '=', 'rfid_history.operator_id')
                        ->whereNull('rfid_history.assigned_till');
                })
                ->get(['operator.*', 'rfid_history.rfid']);
            if ($operators == null) {
                Log::warning("Not found Operator");
                throw new NotFoundResourceException();
            }
            return $operators;
        } else if (!$queryBuilder->unAssigned && $queryBuilder->assigned) {
            $operators =  DB::table('operator')
                ->join('rfid_history', function ($join) {
                    $join->on('operator.id', '=', 'rfid_history.operator_id')
                        ->whereNull('rfid_history.assigned_till');
                })
                ->get(['operator.*', 'rfid_history.rfid']);
            if ($operators == null) {
                Log::warning("Not found Operator");
                throw new NotFoundResourceException();
            }
            return $operators;
        } else if ($queryBuilder->unAssigned && !$queryBuilder->assigned) {
            $operators =  DB::table('operators')
                ->join('rfid_history', function ($join) {
                    $join->on('operators.id', '=', 'rfid_history.operator_id')
                        ->whereNotNull('rfid_history.assigned_till');
                })
                ->get(['operators.*', 'rfid_history.rfid']);
            if ($operators == null) {
                Log::warning("Not found Operator");
                throw new NotFoundResourceException();
            }
            return $operators;
        }
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
        $rfidHistory = $this->findCurrentAssignedRfid($operatorId);
        if ($rfidHistory->assigned_till != null) {
            throw new AlreadyUsedException();
        }
        $rfidHistory->assigned_till = Carbon::parse(new DateTime());
        $rfidHistory->update();
        $rfid = Rfid::where('rfid', $rfidHistory->rfid)->first();
        $rfid->current_operator_id = 0;
        $rfid->update();
        Log::info('Rfid Removed for Operator');
    }

    public function findCurrentAssignedRfid($id)
    {
        $rfids =  RfidHistory::where([
            ['operator_id', $id],
            ['assigned_till', null]
        ])->first();
        if ($rfids == null) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }
}
