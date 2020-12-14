<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\DriverDto;
use App\Services\Interfaces\DriverServiceInterface;
use Illuminate\Http\Request;

class DriverController extends Controller
{

    private DriverServiceInterface $driverService;

    public function __construct(DriverServiceInterface $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = $this->driverService->findAll();

        return response()->json([
            'data' => $drivers
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateDriverData = $request->validate([
            'driver_id' => 'required',
            'driver_name' => 'required',
            'driver_status' => 'required',
            'driver_license_no' => 'required',
        ]);
        $driver = new DriverDto();
        $driver->driverId = $validateDriverData['driver_id'];
        $driver->driverName = $validateDriverData['driver_name'];
        $driver->driverStatus = $validateDriverData['driver_status'];
        $driver->driverLicenseNo = $validateDriverData['driver_license_no'];
        $this->driverService->create($driver);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show($driverId)
    {
        $driver = $this->driverService->findById($driverId);

        return response()->json([
            'data' => $driver
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $driverId)
    {
        $validateDriverData = $request->validate([
            'driver_name' => 'required',
            'driver_status' => 'required',
            'driver_license_no' => 'required',
        ]);
        $driver = new DriverDto();
        $driver->driverId = $driverId;
        $driver->driverName = $validateDriverData['driver_name'];
        $driver->driverStatus = $validateDriverData['driver_status'];
        $driver->driverLicenseNo = $validateDriverData['driver_license_no'];
        $this->driverService->update($driver);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy($driverId)
    {
        $this->driverService->delete($driverId);
        return response(['message' => 'Success!'], 200);
    }
}
