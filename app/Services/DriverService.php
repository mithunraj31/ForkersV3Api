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
        $driver->driver_id = $model->driverId;
        $driver->driver_name = $model->driverName;
        $driver->driver_status = $model->driverStatus;
        $driver->driver_license_no = $model->driverLicenseNo;
        $driver->save();
        Log::info('Driver has been created');
    }

    public function update(DriverDto $model)
    {
        Log::info('Updating Driver', $model->toArray());
        $driver = $this->findById($model->driverId);
        $driver->driver_name = $model->driverName;
        $driver->driver_status = $model->driverStatus;
        $driver->driver_license_no = $model->driverLicenseNo;
        $driver->update();
        Log::info('Driver has been updated');
    }

    public function findById($driverId)
    {
        $driver =  Driver::find($driverId);
        if ($driver == null) {
            Log::warning("Not found Driver by ID $driverId");
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

    public function delete($driverId)
    {
        $driver = $this->findById($driverId);
        Log::info('Deleting Driver data', (array)  $driver);
        $driver->delete();
        Log::info("Deleted Driver by ID $driverId");
    }
}
