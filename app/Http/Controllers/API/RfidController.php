<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\RfidDto;
use App\Services\Interfaces\RfidServiceInterface;
use Illuminate\Http\Request;

class RfidController extends Controller
{

    private RfidServiceInterface $rfidService;

    public function __construct(RfidServiceInterface  $rfidService)
    {
        $this->rfidService = $rfidService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rfid = $this->rfidService->findAll();

        return response()->json($rfid, 200);
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
            'rfid' => 'required',
            'rfid_name' => 'required',
            'created_by' => 'required|exists:App\Models\User,id',
        ]);
        $rfid = new RfidDto();
        $rfid->rfid = $validateRfidData['rfid'];
        $rfid->rfidName = $validateRfidData['rfid_name'];
        $rfid->createdBy = $validateRfidData['created_by'];
        $this->rfidService->create($rfid);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rfid $rfid
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rfid = $this->rfidService->findById($id);

        return response()->json([
            'data' => $rfid
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rfid      $rfid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validateRfidData = $request->validate([
            'rfid' => 'required',
            'rfid_name' => 'required',
            'created_by' => 'required|exists:App\Models\User,id',
        ]);
        $rfid = new rfidDto();
        $rfid->id = $id;
        $rfid->rfid = $validateRfidData['rfid'];
        $rfid->rfidName = $validateRfidData['rfid_name'];
        $rfid->createdBy = $validateRfidData['created_by'];
        $this->rfidService->update($rfid);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rfid      $rfid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->rfidService->delete($id);
        return response(['message' => 'Success!'], 200);
    }
}
