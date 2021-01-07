<?php

namespace App\Services;

use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Exceptions\StonkamInvalidRequestException;
use App\Http\Resources\DeviceResource;
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

        //create user in stonkam
        $this->createDeviceInStonkam($request);

        //create user in database
        $device = new Device();
        $device->id = $request->id;
        $device->customer_id = $request->customer_id;
        $device->channel_number = $request->channel_number;
        $device->plate_number = $request->plate_number;
        $device->owner_id = Auth::user()->id;

        return $device->save();
    }

    public function update(DeviceDto $request, Device $device)
    {
        // get required data from db
        if ($request->plate_number || $request->channel_number) {
            if (!$request->plate_number) {
                $request->plate_number = $device->plate_number;
            }
            if(!$request->channel_number){
                $request->channel_number = $device->channel_number;
            }
        }
        // update stonkam device
        $session = $this->stonkamService->refreshAccessToken();
        $this->updateDeviceInStonkam($request, $session);

        // update db
        // when customer is needed to update stonkam need to delete and re enter.
        if($request->customer_id) {

            $currentCustomer = $device->customer;
            $newCustomer = Customer::find($request->customer_id);
            $request->stk_user = $newCustomer->stk_user;

            //delete from current stonkam group
            $this->deleteDeviceInStonkamGroup($request,$currentCustomer->stk_user,$session);

            //delete from current stonkam sub user
            $this->deleteDeviceInStonkamUser($request,$currentCustomer->stk_user, $session);

            // add to new stonkam group
            $this->createDeviceInStonkamGroup($request, $session);

            // add to customer
            $this->addDeviceToStonkamUser($request->id, $request->stk_user, $session);

            $device->customer_id = $request->customer_id;
        }
        $device->channel_number = $request->channel_number;
        $device->plate_number = $request->plate_number;

        return $device->save();
    }

    public function findById(Device $device)
    {
        return new DeviceResource($device->load('location, vehicle'));
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
        $this->createDeviceInStonkamGroup($request, $session);

        // add to admin
        $this->addDeviceToStonkamUser($request->id, config('stonkam.auth.admin.username'), $session);

        // add to customer
        $this->addDeviceToStonkamUser($request->id, $request->stk_user, $session);
    }


    private function createDeviceInStonkamGroup(DeviceDto $request, $session)
    {

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

    private function addDeviceToStonkamUser($id, $stk_user, $session)
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

    private function updateDeviceInStonkam(DeviceDto $request, $session)
    {

        $endpoint = config('stonkam.hostname') . "/EditDevice/100";

        $data = [
            'DeviceId' => $request->id,
            'UserName' => config('stonkam.auth.admin.username'),
            'SessionId' => $session,
            'NewPlateNumber' => $request->plate_number,
            'ChannelNumber' => $request->channel_number
        ];
        Log::info("Requesting stonkam server for Update device - $request->id");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Update device success!!');
    }
    private function deleteDeviceInStonkamGroup(DeviceDto $request,$groupName,$session)
    {

        $endpoint = config('stonkam.hostname') . "/DelDeviceForGroup/100";

        $data = [
            'UserName' => config('stonkam.auth.admin.username'),
            'DeviceId' => $request->id,
            'GroupName' => $groupName,
            'SessionId' => $session
        ];
        Log::info("Requesting stonkam server for delete device from group - $request->id");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('delete device from group success!!');
    }
    private function deleteDeviceInStonkamUser(DeviceDto $request,$username,$session)
    {

        $endpoint = config('stonkam.hostname') . "/DelDeviceForUser/100";

        $data = [
            'ParentUserName' =>config('stonkam.auth.admin.username'),
            'UserName' => $username,
            'DeviceId' => $request->id,
            'SessionId' => $session
        ];
        Log::info("Requesting stonkam server for delete device from user - $request->id");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('delete device from user success!!');
    }
}
