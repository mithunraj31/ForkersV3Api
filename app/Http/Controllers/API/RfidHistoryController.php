<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\RfidHistoryServiceInterface;
use App\Utils\CollectionUtility;
use DateTime;
use Illuminate\Http\Request;

class RfidHistoryController extends Controller
{
    private RfidHistoryServiceInterface $rfidHistoryService;

    public function __construct(RfidHistoryServiceInterface $rfidHistoryService)
    {
        $this->rfidHistoryService = $rfidHistoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rfid = $this->rfidHistoryService->findAll();
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
    public function assignOperator(Request $request)
    {
        $validateRfidData = $request->validate([
            'rfid' => 'required',
            'operator_id' => 'required|exists:App\Models\Driver,id'
        ]);
        $rfid = new RfidHistoryDto();
        $rfid->rfid = $validateRfidData['rfid'];
        $rfid->operatorId = $validateRfidData['operator_id'];
        $rfid->assignedFrom = new DateTime();
        $rfid->assignedTill = null;
        $this->rfidHistoryService->assignOperator($rfid);
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
        $rfidHistory = $this->rfidHistoryService->findrfIdHistory($rfid);
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
    public function removeOperator($rfid)
    {
        $this->rfidHistoryService->removeOperator($rfid);
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
}
