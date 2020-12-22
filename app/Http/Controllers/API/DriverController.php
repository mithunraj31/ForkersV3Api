<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\DriverDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\DriverServiceInterface;
use App\Utils\CollectionUtility;
use DateTime;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $drivers = $this->driverService->findAll();
        return response($drivers, 200);
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

    public function assignRfid(Request $request)
    {
        $validateDriverData = $request->validate([
            'id' => 'required|exists:App\Models\Driver,id',
            'rfid' => 'required|exists:App\Models\Rfid,rfid',
        ]);
        $driver = new RfidHistoryDto();
        $driver->rfid = $validateDriverData['rfid'];
        $driver->operatorId = $validateDriverData['id'];
        $driver->assignedFrom = new DateTime();
        $driver->assignedTill = null;
        $this->driverService->assignRfid($driver);
        return response(['message' => 'Success!'], 200);
    }

    public function removeRfid($operatorId)
    {
        $this->driverService->removeRfid($operatorId);
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

        return response([
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
}
