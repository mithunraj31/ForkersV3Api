<?php

namespace App\Services;


use App\Models\Driver;
use App\Models\DTOs\DriverDto;
use App\Services\Interfaces\DriverServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class DriverService extends ServiceBase implements DriverServiceInterface
{
    public function create(DriverDto $model)
    {
        Log::info('Creating Driver', $model->toArray());
        $driver = new Driver();
        $driver->operator_id = $model->operatorId;
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
        $driver->operator_id = $model->operatorId;
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
        $drivers =  Driver::all();
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
}
