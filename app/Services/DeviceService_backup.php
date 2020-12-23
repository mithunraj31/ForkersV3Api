<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Regular;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\DeviceServiceInterface_backup;
use App\Services\Interfaces\StonkamServiceInterface;
use BaoPham\DynamoDb\RawDynamoDbQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Support\Facades\Log;
use DateTime;

class DeviceService_backup extends ServiceBase implements DeviceServiceInterface_backup
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
        // $drives = Drive::getDriveSummary($deviceId, $startTime, $endTime);
        $drives = $this->getDriveSummaryByDynamo($deviceId, $startTime, $endTime);
        // $durations = $this->calculatDuration($drives);
        // return ['data' => $drives, 'duration' => $durations];
        return $drives;
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

        $startDate = $this->formatDate($start);
        $endDate = $this->formatDate($end);
        $time1 = new Datetime('NOW');
        $data = Regular::where('device', $deviceId)
            ->where('datetime', 'between', [$startDate, $endDate])
            ->where('lat', '!=', '0.0')
            ->limit(10000)->toDynamoDbQuery();
        $time2 = new Datetime('NOW');
        return ['data' => $data];
    }

    private function formatDate($date)
    {
        $newDate = new DateTime($date);
        $dateStr = $newDate->format('Y-m-d H:i:s');
        $dateStr = str_replace(' ', 'T', $dateStr) . 'Z';
        return $dateStr;
    }
    private function addFormatDate($date,$seconds)
    {
        $newDate = new DateTime($date);
        $newDate->modify("+1$seconds second");
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

    private function getDriveSummaryByDynamo($deviceId, $startTime, $endTime)
    {
        // declare types of regular
        $startEngine = 2;
        $stopEngine = 3;
        $registerDriver = 4;
        // get regular data
        $startDate = $this->formatDate($startTime);
        $endDate = $this->formatDate($endTime);
        $regularData = $this->getRegularByTimeRange($deviceId, $startDate, $endDate);
        if ($regularData->count() > 0) {
            // when start with not engine start
            if ($regularData[0]->type != $startEngine) {
                //get first start engine from dynamo
                $tempQueryStartDate = $regularData[0]->datetime;
                $startData = $this->getRegularStart($deviceId, $tempQueryStartDate);
                if ($startData->count() > 0) {
                    // remove garbage values
                    $tempQueryStartDate = $startData[0]->datetime;
                    $tempQueryStopDate = $regularData[0]->datetime;
                    for ($i = 0; $i < $regularData->count() && $regularData[$i]->type != $stopEngine; $i++) {
                        $tempQueryStopDate = $regularData[$i]->datetime;
                        // unset($regularData[$i]);
                        $regularData->forget($i);
                    }
                    //get all previous Data until it ends

                    $prefixData =  $this->getRegularByTimeRange($deviceId, $tempQueryStartDate, $tempQueryStopDate);
                    // concat new data
                    $regularData = $this->concatCollection($prefixData, $regularData);
                }
            }
            // when end is not Engine Stop
            if ($regularData[$regularData->count() - 1]->type != $stopEngine) {
                // get last stop engine from dynamo
                $tempQueryStopDate = $regularData[$regularData->count() - 1]->datetime;
                $stopData = $this->getRegularEnd($deviceId, $tempQueryStopDate);

                if ($stopData->count() > 0) {
                    // remove Garbage values
                    $tempQueryStartDate = $regularData[$regularData->count() - 1]->datetime;
                    $tempQueryStopDate = $stopData[0]->datetime;

                    // get all post data
                    $postfixData = $this->getRegularByTimeRange($deviceId, $this->addFormatDate($tempQueryStartDate,1), $tempQueryStopDate);
                    // Concat data
                    $regularData = $this->concatCollection($regularData, $postfixData);
                }
            }
            // populate Drive Data
            $driveData = $this->populateDriveData($regularData);
            return $driveData;
        } else {
            return [];
        }
    }

    private function concatCollection($firstCollection, $secondCollection)
    {
        foreach ($secondCollection as $regular) {
            $firstCollection->push($regular);
        }
        return $firstCollection;
    }
    private function getRegularByTimeRange($deviceId, $startTime, $endTime)
    {
        return Regular::where(['device' => $deviceId])
            ->where('datetime', 'between', [$startTime, $endTime])
            ->where('type', '!=', '1')
            ->limit(10000)->get();
    }
    private function getRegularStart($deviceId, $time)
    {
        return Regular::where(['device' => $deviceId])
            ->where('datetime', '<', $time)
            ->where('type', '2')
            ->decorate(function (RawDynamoDbQuery $raw) {
                // desc order
                $raw->query['ScanIndexForward'] = false;
            })
            ->limit(1)->get();
    }
    private function getRegularEnd($deviceId, $time)
    {
        return Regular::where(['device' => $deviceId])
            ->where('datetime', '>', $time)
            ->where('type', '3')
            ->limit(1000)->get();
    }
    private function populateDriveData($regularData)
    {
        $driveSummary = [];
        $i = 0;
        foreach ($regularData as $i => $regular) {
            // $rData = $regularData->getOne($i);
            //Start engine
            if($i==62){
                $k=0;
            }
            if ($regular->type == 2) {
                $engineStart = new Datetime($regular->datetime);
                $driveData = [
                    'engine_started_at' => $engineStart->format('Y-m-d H:i:s'),
                    'engine_stoped_at' => null
                ];
                $driverData = [];

                foreach ($regularData as $j => $subRegular) {
                    if ($j >= $i) {
                        $dd = [
                            'driver_id' => null,
                            'drive_start_at' => null,
                            'drive_ended_at' => null
                        ];
                        if (($subRegular->type == 2 || $subRegular->type == 4) && $subRegular->driverId != "") {
                            $dd = [
                                'driver_id' => $subRegular->driverId,
                                'drive_start_at' => $subRegular->datetime,
                                'drive_ended_at' => null
                            ];
                            if (count($driverData) > 0) {
                                $driverData[count($driverData) - 1]['drive_ended_at'] = $subRegular->datetime;
                            }
                            array_push($driverData, $dd);
                        }
                        if ($subRegular->type == 3) {
                            if (count($driverData) > 0) {
                                $driverData[count($driverData) - 1]['drive_ended_at'] = $subRegular->datetime;
                            }
                            $driveData['engine_stoped_at'] = $subRegular->datetime;
                            $driveData['driver_data'] = $driverData;
                            array_push($driveSummary, $driveData);
                            break;
                        }
                        // last data and end is not found
                        if ($j == ($regularData->count() - 1) && $subRegular->type != 3) {
                            $driveData['driver_data'] = $driverData;
                            array_push($driveSummary, $driveData);
                        }
                    }
                }
            }
        }
        return $driveSummary;
    }
}
