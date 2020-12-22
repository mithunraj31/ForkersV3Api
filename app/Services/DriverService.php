<?php

namespace App\Services;


use App\Models\Driver;
use App\Models\DTOs\DriverDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Models\Rfid;
use App\Models\RfidHistory;
use App\Services\Interfaces\DriverServiceInterface;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\AlreadyUsedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class DriverService extends ServiceBase implements DriverServiceInterface
{
    public function create(DriverDto $model)
    {
        Log::info('Creating Driver', $model->toArray());
        $driver = new Driver();
        $driver->name = $model->name;
        $driver->dob = $model->dob;
        $driver->address = $model->address;
        $driver->license_no = $model->licenseNo;
        $driver->license_received_date = $model->licenseReceivedDate;
        $driver->license_renewal_date = $model->licenseRenewalDate;
        $driver->license_location = $model->licenseLocation;
        $driver->phone_no = $model->phoneNo;
        $driver->save();
        Log::info('Driver has been created');
    }

    public function update(DriverDto $model)
    {
        Log::info('Updating Driver', $model->toArray());
        $driver = $this->findById($model->id);
        $driver->name = $model->name;
        $driver->dob = $model->dob;
        $driver->address = $model->address;
        $driver->license_no = $model->licenseNo;
        $driver->license_received_date = $model->licenseReceivedDate;
        $driver->license_renewal_date = $model->licenseRenewalDate;
        $driver->license_location = $model->licenseLocation;
        $driver->phone_no = $model->phoneNo;
        $driver->update();
        Log::info('Driver has been updated');
    }

    public function findById($id)
    {
        $driver =  Driver::find($id);
        if ($driver == null) {
            Log::warning("Not found Driver by ID $id");
            throw new NotFoundResourceException();
        }
        return $driver;
    }


    public function findAll()
    {
        $drivers =  DB::table('operators')
            ->leftJoin('rfid_history', function ($join) {
                $join->on('operators.id', '=', 'rfid_history.operator_id')
                    ->whereNull('rfid_history.assigned_till');
            })
            ->get(['operators.*', 'rfid_history.rfid']);
        if ($drivers->count() == 0) {
            Log::warning("Not found Drivers");
            throw new NotFoundResourceException();
        }



        return $drivers;
    }



    public function delete($id)
    {
        $driver = $this->findById($id)->first();
        Log::info('Deleting Driver data', (array)  $driver);
        $driver->delete();
        Log::info("Deleted Driver by ID $id");
    }

    public function assignRfid(RfidHistoryDto $model)
    {
        Log::info('Assigning Rfid ', $model->toArray());
        $rfidHistory = new RfidHistory();
        $rfidHistory->rfid = $model->rfid;
        $rfidHistory->operator_id = $model->operatorId;
        $rfidHistory->assigned_from = $model->assignedFrom;
        $rfidHistory->assigned_till = $model->assignedTill;
        $rfid = Rfid::where('rfid', $model->rfid)->first();
        if ($rfid->count() == 0) {
            Log::warning("Not found rfid for  $model->rfid");
            throw new NotFoundResourceException();
        } else if ($rfid->current_operator_id != 0) {
            throw new AlreadyUsedException();
        }
        $rfid->current_operator_id = $model->operatorId;
        $rfidHistory->save();
        $rfid->update();
        Log::info('Rfid has been assigned');
    }

    public function removeRfid($id)
    {
        Log::info('Removing rfid for Operator ');
        $rfidHistory = $this->findCurrentAssignedRfid($id);
        if ($rfidHistory->assigned_till != null) {
            throw new AlreadyUsedException();
        }
        $rfidHistory->assigned_till = new DateTime();
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
        if ($rfids->count() == 0) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfids;
    }
}
