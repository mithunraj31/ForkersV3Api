<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\OperatorDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\OperatorServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperatorController extends Controller
{

    private OperatorServiceInterface $operatorService;

    public function __construct(OperatorServiceInterface $operatorService)
    {
        $this->operatorService = $operatorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queryBuilder = new OperatorDto();
        $queryBuilder->unAssigned = filter_var($request->unAssigned, FILTER_VALIDATE_BOOLEAN);
        $queryBuilder->assigned = filter_var($request->assigned, FILTER_VALIDATE_BOOLEAN);
        $queryBuilder->perPage = $request->query('perPage');
        $operators = $this->operatorService->findAll($queryBuilder);
        return response($operators, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateOperatorData = $request->validate([
            'name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'license_no' => 'required',
            'license_received_date' => 'required',
            'license_renewal_date' => 'required',
            'license_location' => 'required',
            'phone_no' => 'required',
        ]);
        $operator = new OperatorDto();
        $operator->name = $validateOperatorData['name'];
        $operator->dob = $validateOperatorData['dob'];
        $operator->address = $validateOperatorData['address'];
        $operator->licenseNo = $validateOperatorData['license_no'];
        $operator->licenseReceivedDate = $validateOperatorData['license_received_date'];
        $operator->licenseRenewalDate = $validateOperatorData['license_renewal_date'];
        $operator->licenseLocation = $validateOperatorData['license_location'];
        $operator->phoneNo = $validateOperatorData['phone_no'];
        $this->operatorService->create($operator);
        return response(['message' => 'Success!'], 200);
    }

    public function assignRfid($operatorId, $rfid)
    {
        $operator = new RfidHistoryDto();
        $operator->rfid = $rfid;
        $operator->operatorId = $operatorId;
        $operator->assignedFrom = Carbon::now();
        $operator->assignedTill = null;
        $this->operatorService->assignRfid($operator);
        return response(['message' => 'Success!'], 200);
    }

    public function removeRfid($operatorId, $rfid)
    {
        $this->operatorService->removeRfid($operatorId, $rfid);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show($operatorId)
    {
        $operator = $this->operatorService->findById($operatorId);

        return response([
            'data' => $operator
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $operatorId)
    {
        $validateOperatorData = $request->validate([
            'name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'license_no' => 'required',
            'license_received_date' => 'required',
            'license_renewal_date' => 'required',
            'license_location' => 'required',
            'phone_no' => 'required',
        ]);
        $operator = new OperatorDto();
        $operator->id = $operatorId;
        $operator->name = $validateOperatorData['name'];
        $operator->dob = $validateOperatorData['dob'];
        $operator->address = $validateOperatorData['address'];
        $operator->licenseNo = $validateOperatorData['license_no'];
        $operator->licenseReceivedDate = $validateOperatorData['license_received_date'];
        $operator->licenseRenewalDate = $validateOperatorData['license_renewal_date'];
        $operator->licenseLocation = $validateOperatorData['license_location'];
        $operator->phoneNo = $validateOperatorData['phone_no'];
        $this->operatorService->update($operator);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function destroy($operatorId)
    {
        $this->operatorService->delete($operatorId);
        return response(['message' => 'Success!'], 200);
    }
}
