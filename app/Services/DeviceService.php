<?php

namespace App\Services;

use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Exceptions\StonkamInvalidRequestException;
use App\Http\Resources\DeviceResourceCollection;
use App\Models\Customer;
use App\Models\Device;
use App\Models\DTOs\DeviceDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeviceService extends ServiceBase implements DeviceServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }


    public function create(DeviceDto $request)
    {

        if(!$request->customer_id){
            $request->customer_id = Auth::user()->customer_id;
        }
        // find stk user
        $customer = Customer::find($request->customer_id);
        $request->stk_user = $customer->stk_user;

        //create user in stonkam
        $this->createDeviceInStonkam($request);

        //create user in database
        $device = new Device();
        $device->id = $request->id;
        $device->customer_id = $request->customer_id;
        $device->stk_user = $request->stk_user;
        $device->channel_number = $request->channel_number;
        $device->plate_number = $request->plate_number;
        $device->owner_id = Auth::user()->id;

        return $device->save();
    }

    public function update(DeviceDto $request, Device $device)
    {

        $device->customer_id = $request->customer_id;
        $device->group_id = $request->group_id;
        $device->stk_user = $request->stk_user;
        $device->channel_number = $request->channel_number;
        $device->plate_number = $request->plate_number;
        $device->device_type = 'DV426';

        return $device->save();

    }

    public function findById(Device $device)
    {
        return $device;
    }

    public function getAll($perPage = 15)
    {
        return new DeviceResourceCollection(Device::with('customer')->paginate($perPage));
    }

    public function delete(Device $device)
    {
        return $device->delete();

    }

    private function createDeviceInStonkam(DeviceDto $request)
    {
        $session = $this->stonkamService->refreshAccessToken();
        // add to group
        $this->createDeviceInStonkamGroup($request,$session);

        // add to admin
        $this->addDeviceToStonkamUser($request->id,config('stonkam.auth.admin.username'),$session);

        // add to customer
        $this->addDeviceToStonkamUser($request->id,$request->stk_user ,$session);
    }


    private function createDeviceInStonkamGroup(DeviceDto $request, $session){

        $endpoint = config('stonkam.hostname') . "/AddDeviceForGroup/100";

        $data = [
            'GroupName' => $request->stk_user,
            'PlateNumber' => strval($request->id),
            'DeviceId' => $request->id,
            'DeviceType' => 'DV426',
            'ChannelNumber' => $request->channel_number,

            'UserName' => config('stonkam.auth.admin.username'),
            'SessionId' => $session
        ];
        Log::info("Requesting stonkam server for creating new device- $request->id");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam device success!!');
    }

    private function addDeviceToStonkamUser($id,$stk_user,$session)
    {
        $endpoint = config('stonkam.hostname') . "/AddDeviceForUser/100";

        $data = [
            'UserName' => $stk_user,
            'DeviceId' => $id,

            'ParentUserName' => config('stonkam.auth.admin.username'),
            'SessionId' => $session
        ];
        Log::info("Requesting stonkam server for add device to user- $id -> $stk_user");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Add device to user success!!');
    }

}
