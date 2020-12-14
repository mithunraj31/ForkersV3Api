<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\DriverDto;
use App\Services\Interfaces\DriverServiceInterface;
use App\Utils\CollectionUtility;
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
    public function index(Request $request)
    {
        $drivers = $this->driverService->findAll();
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($drivers, $perPage);
        return response($result, 200);
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
            'name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'license_no' => 'required',
            'license_received_date' => 'required',
            'license_renewal_date' => 'required',
            'license_location' => 'required',
            'phone_no' => 'required',
        ]);
        $driver = new DriverDto();
        $driver->driverId = $validateDriverData['driver_id'];
        $driver->name = $validateDriverData['name'];
        $driver->dob = $validateDriverData['dob'];
        $driver->address = $validateDriverData['address'];
        $driver->licenseNo = $validateDriverData['license_no'];
        $driver->licenseReceivedDate = $validateDriverData['license_received_date'];
        $driver->licenseRenewalDate = $validateDriverData['license_renewal_date'];
        $driver->licenseLocation = $validateDriverData['license_location'];
        $driver->phoneNo = $validateDriverData['phone_no'];
        $this->driverService->create($driver);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $driver = $this->driverService->findById($id);

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
    public function update(Request $request, $id)
    {
        $validateDriverData = $request->validate([
            'driver_id' => 'required',
            'name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'license_no' => 'required',
            'license_received_date' => 'required',
            'license_renewal_date' => 'required',
            'license_location' => 'required',
            'phone_no' => 'required',
        ]);
        $driver = new DriverDto();
        $driver->id = $id;
        $driver->driverId = $validateDriverData['driver_id'];
        $driver->name = $validateDriverData['name'];
        $driver->dob = $validateDriverData['dob'];
        $driver->address = $validateDriverData['address'];
        $driver->licenseNo = $validateDriverData['license_no'];
        $driver->licenseReceivedDate = $validateDriverData['license_received_date'];
        $driver->licenseRenewalDate = $validateDriverData['license_renewal_date'];
        $driver->licenseLocation = $validateDriverData['license_location'];
        $driver->phoneNo = $validateDriverData['phone_no'];
        $this->driverService->update($driver);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->driverService->delete($id);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegularDataByDriverId(Request $request, $driverId)
    {
        $drivers = $this->driverService->findRegularDataByDriverId($driverId);
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($drivers, $perPage);
        return response()->json([
            'data' => $result
        ], 200);
    }
}
