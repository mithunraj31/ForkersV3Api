<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\RfidDto;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\RfidServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    private RfidServiceInterface $rfidService;

    public function __construct(RfidServiceInterface $rfidService)
    {
        $this->rfidService = $rfidService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rfid = $this->rfidService->findAll();
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($rfid, $perPage);
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
        $validateRfidData = $request->validate([
            'id' => 'required',
            'customerId' => 'required',
            'ownerId' => 'required',
            'groupId' => 'required',
        ]);
        $rfid = new RfidDto();
        $rfid->id = $validateRfidData['id'];
        $rfid->customerId = $validateRfidData['customerId'];
        $rfid->ownerId = $validateRfidData['ownerId'];
        $rfid->groupId = $validateRfidData['groupId'];
        $this->rfidService->create($rfid);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\rfid  $rfid
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rfid = $this->rfidService->findById($id);

        return response([
            'data' => $rfid
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\   rfid  $   rfid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $rfid)
    {
        $validateRfidData = $request->validate([
            'customerId' => 'required',
            'ownerId' => 'required',
            'groupId' => 'required',

        ]);
        $rfids = new RfidDto();
        $rfids->id = $rfid;
        $rfids->customerId = $validateRfidData['customerId'];
        $rfids->ownerId = $validateRfidData['ownerId'];
        $rfids->groupId = $validateRfidData['groupId'];
        $this->rfidService->update($rfids);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\   rfid  $   rfid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->rfidService->delete($id);
        return response(['message' => 'Success!'], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignOperator($rfid, $operatorId)
    {
        $rfid = new RfidHistoryDto();
        $rfid->rfid = $rfid;
        $rfid->operatorId = $operatorId;
        $rfid->assignedFrom = Carbon::parse(new DateTime());
        $rfid->assignedTill = null;
        $this->rfidService->assignOperator($rfid);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\rfid  $rfid
     * @return \Illuminate\Http\Response
     */
    public function findrfIdHistory(Request $request, $rfid)
    {
        $rfidHistory = $this->rfidService->findrfIdHistory($rfid);
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $result = CollectionUtility::paginate($rfidHistory, $perPage);
        return response($result, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\   rfid  $   rfid
     * @return \Illuminate\Http\Response
     */
    public function removeOperator($rfid, $operatorId)
    {
        $this->rfidService->removeOperator($rfid);
        return response(['message' => 'Success!'], 200);
    }
}
