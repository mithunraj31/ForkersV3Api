<?php

namespace App\Services;

use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Support\Facades\Log;

class StonkamDeviceService implements DeviceServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }


    public function getAllDevice()
    {
        Log::info('Getting all device informations');
        $devices = $this->getAllDevicesFromStonkam();

        $deviceLocations = $this->getDeviceLocations($devices);

        Log::info('Mapping devices to array');
        return $this->mapDevicesToArray($devices, $deviceLocations);
    }

    private function getAllDevicesFromStonkam()
    {
        Log::info('Getting all device informatons from stonkam');
        $sessionId = $this->stonkamService->refreshAccessToken();

        $endpoint = config('stonkam.hostname') . '/GetDeviceList/10000';
        $response = Http::get($endpoint, [
            'SessionId' => $sessionId,
            'User' => config('stonkam.stk_user')
        ]);

        if (!$response->ok()) {
            Log::warning('Device not found');
            throw new NotFoundResourceException();
        }
        Log::info('All devices is fetched successfully');
        $content = $response->json();
        if (!$content['DeviceList'] || count($content['DeviceList']) == 0) {
            return [];
        }

        return $content['DeviceList'];
    }

    private function getDeviceLocations($devices)
    {
        Log::info('Getting the device location');
        $deviceIdCollections = collect($devices)->map(function ($device) {
            return ['DeviceId' => $device['DeviceId']];
        });

        $data = [
            'UserName' => config('stonkam.auth.admin.username'),
            'DeviceList' => $deviceIdCollections->all()
        ];
        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = config('stonkam.hostname') . '/GetDevicesGps/' . count($devices) . "?CmsClientId=0&IsNeedPush=0&SessionId=$sessionId";
        Log::info('Post request is sent for stonkam  "/GetDevicesGps/"');
        $response = Http::post($endpoint, $data);

        if (!$response->ok()) {
            Log::warning('Something went wrong while fetching the location of device');
            throw new NotFoundResourceException();
        }

        Log::info('Device location is fetched successfully');
        $content = $response->json();
        return $content['DevicesGps'];
    }

    private function mapDevicesToArray($devices, $deviceLocations): Collection
    {
        $locations = collect($deviceLocations)->keyBy('DeviceId');
        $mappedDevices = collect($devices)->map(function ($device) use ($locations) {
            $deviceId = $device['DeviceId'];
            $deviceGps = $locations->get($deviceId);
            return [
                'id' => $deviceId,
                'location' => [
                    'lat' => $deviceGps != null ? $deviceGps['Latitude'] : '0.000000',
                    'lng' => $deviceGps != null ? $deviceGps['Longitude'] : '0.000000',
                ],
                'detail' => [
                    'plate_number' => $device['PlateNumber'],
                    'device_id' => $deviceId,
                    'scan_code' => $device['ScanCode'],
                    'channel_number' => $device['ChannelNumber'],
                    'group_name' => $device['GroupName'],
                    'tcp_server_addr' => $device['TcpServerAddr'],
                    'tcp_stream_out_port' => $device['TcpStreamOutPort'],
                    'udp_server_addr' => $device['UdpServerAddr'],
                    'udp_stream_out_port' => $device['UdpStreamOutPort'],
                    'net_type' => $device['NetType'],
                    'device_type' => $device['DeviceType'],
                    'is_active' => $device['IsActive'],
                    'is_online' => $device['IsOnline'],
                ],
                'active' => $device['IsActive'],
                'online' => $device['IsOnline'],
            ];

        });
        Log::info('Mapping devices to array is successful');
        return $mappedDevices;
    }
}
