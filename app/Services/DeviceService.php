<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DTOs\DeviceDto;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Facades\Auth;

class DeviceService extends ServiceBase implements DeviceServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    public function create(DeviceDto $request)
    {
        //create device in stonkam
        $this->createDeviceInStonkam($request);

        $device = new Device();

        $device->id = $request->id;
        $device->channel_number = $request->channel_number;
        $device->plate_number = $request->plate_number;
        $device->assigned = false;
        $device->device_type = $request->device_type;

        $device->owner_id = Auth::user()->id;
        if ($request->customer_id) {
            $device->customer_id = $request->customer_id;
        } else {
            $device->customer_id = Auth::user()->customer_id;
        }
    }

    public function update(DeviceDto $request, Device $device)
    {
    }

    public function findById(Device $device)
    {
    }

    public function getAll($perPage)
    {
    }

    public function delete(Device $device)
    {
    }

    private function createDeviceInStonkam(DeviceDto $request){
        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = config('stonkam.hostname') . "/AddUser/100";

        $data = [
            'UserName' => $customer->stk_user,
            'Password' => config('stonkam.auth.customer.password'),
            'ParentUserName' => config('stonkam.auth.admin.username'),
            'SessionId' => $sessionId
        ];
    }
}
