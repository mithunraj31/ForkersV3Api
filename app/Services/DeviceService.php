<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Drive;
use App\Models\Regular;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Support\Facades\Log;
use DateTime;

class DeviceService extends ServiceBase implements DeviceServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    public function getAllDevice()
    {
        // Look SQL query at /database/migrations/2020_12_09_040748_create_latest_devices_view.php
        $devices = Device::getLatestDevice();
        return collect($devices)->map(function ($d) {
            return (array) $d;
         });
    }

    public function getAllDeviceStonkam()
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

    public function getDriveSummary($deviceId, $startTime, $endTime)
    {
        $drives = Drive::getDriveSummary($deviceId, $startTime, $endTime);
        $durations = $this->calculatDuration($drives);
        return ['data' => $drives, 'duration' => $durations];
    }
    private function calculatDuration($drives)
    {
        $duration = ['engine' => 0, 'drive' => 0];

        foreach ($drives as $drive) {
            if ($drive['engine_started_at'] && $drive['engine_stoped_at']) {
                $start = strtotime($drive['engine_started_at']);
                $end = strtotime($drive['engine_stoped_at']);
                $d = $end - $start;
                $duration['engine'] += $d;

                foreach ($drive['driver_data'] as $driver) {
                    if ($driver['drive_start_at'] && $driver['drive_ended_at']) {
                        $startd = strtotime($driver['drive_start_at']);
                        $endd = strtotime($driver['drive_ended_at']);
                        $dd = $endd - $startd;
                        $duration['drive'] += $dd;
                    }
                }
            }
        }
        return $duration;
    }

    public function getRoute($deviceId, $start, $end)
    {
        $regular = new Regular();
        $startDate = $this->formatDate($start);
        $endDate = $this->formatDate($end);
        $data = $regular->where('device', $deviceId)->where('lat', 'not_contains', '0.0')->where('datetime', 'between', [$startDate, $endDate])->limit(10000)->get();
        return ['data' => $data];
    }

    private function formatDate($date)
    {
        $newDate = new DateTime($date);
        $dateStr = $newDate->format('Y-m-d H:i:s');
        $dateStr = str_replace(' ', 'T', $dateStr) . 'Z';
        return $dateStr;
    }
    private function populateDeviceResult($devices)
    {
        $onlineCount = 0;
        $offlineCount = 0;
        $deviceList = [];
        if (count($devices) > 0) {

            foreach ($devices as $device) {
                if ($device->type == 3) {
                    $device->online = false;
                    array_push($deviceList, $device);
                    $offlineCount += 1;
                } else {
                    $device->online = true;
                    array_push($deviceList, $device);
                    $onlineCount += 1;
                }
            }
        }
        $meta = ['online_count' => $onlineCount, 'offline_count' => $offlineCount];
        return ['data' => $deviceList, 'meta' => $meta];
    }
}
