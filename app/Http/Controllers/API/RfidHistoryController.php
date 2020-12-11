<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\RfidHistoryDto;
use App\Services\Interfaces\RfidHistoryServiceInterface;
use Illuminate\Http\Request;

class RfidHistoryController extends Controller
{

    private RfidHistoryServiceInterface $rfidHistoryService;

    public function __construct(RfidHistoryServiceInterface  $rfidHistoryService)
    {
        $this->rfidHistoryService = $rfidHistoryService;
    }
    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateRfidData = $request->validate([
            'rfid' => 'required|exists:App\Models\Rfid,rfid',
            'driver_id' => 'required|exists:App\Models\Driver,driver_id',
            'begin_time' => 'required',
            'end_time' => 'required'
        ]);
        $rfid = new RfidHistoryDto();
        $rfid->rfid = $validateRfidData['rfid'];
        $rfid->driverId = $validateRfidData['driver_id'];
        $rfid->beginTime = $validateRfidData['begin_time'];
        $rfid->endTime = $validateRfidData['end_time'];
        $this->rfidHistoryService->create($rfid);
        return response(['message' => 'Success!'], 200);
    }
}
