<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Models\Device;
use App\Models\DTOs\DeviceDto;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Utils\CollectionUtility;
use Exception;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    private DeviceServiceInterface $deviceService;

    public function __construct(
        DeviceServiceInterface $deviceService
    )
    {
        $this->deviceService = $deviceService;
    }

    public function index(Request $request)
    {

        return response($this->deviceService->getAll($request->query('perPage')), 200);
    }

    public function show(Device $device)
    {
        return $this->deviceService->update($device);
    }

    public function create(Request $request)
    {
        $deviceRequest = new DeviceDto();
        $deviceRequest->id = $request->id;
        $deviceRequest->plate_number = $request->plate_number;
        $deviceRequest->channel_number = $request->channel_number;
        $deviceRequest->group_id = $request->group_id;
        $deviceRequest->customer_id = $request->customer_id;

        return response($this->deviceService->create($deviceRequest), 200);

    }

    public function update(Request $request, Device $device)
{
        $deviceRequest = new DeviceDto();
        $deviceRequest->plate_number = $request->plate_number;
        $deviceRequest->channel_number = $request->channel_number;
        $deviceRequest->group_id = $request->group_id;
        $deviceRequest->customer_id = $request->customer_id;

        return response($this->deviceService->update($deviceRequest, $device));
    }
    public function delete(Device $device){
        return response($this->deviceService->delete($device),201);

    }
}
