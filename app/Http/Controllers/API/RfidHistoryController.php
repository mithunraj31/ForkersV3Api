<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\RfidHistoryServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
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
    public function removeOperator($rfid, $operatorId)
    {
        $this->rfidHistoryService->removeOperator($rfid);
        return response(['message' => 'Success!'], 200);
    }
}
