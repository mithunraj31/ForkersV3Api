<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Drive;
use App\Models\Regular;
use App\Services\Interfaces\DeviceServiceInterface;
use BaoPham\DynamoDb\RawDynamoDbQuery;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceService implements DeviceServiceInterface
{
    // return device list with online offline status and location
    // when loacation is not available in drive data get from regular data
    public function getAllDevice()
    {
        $sort = Drive::orderBy('time', 'DESC');
        $time1 = new Datetime('NOW');
        $devices = DB::table(DB::raw("({$sort->toSql()}) as d"))->groupBy('device_id')->get();
        $time2 = new Datetime('NOW');
        $endTime = '2020-11-10 05:44:39';
        $startTime = '2020-11-10 04:44:39';
        $l = "select * from `regular` where `latitude` != 0 and time > '$startTime' and time < '$endTime' order by `time` desc";
        // $time3 = new Datetime('NOW');
        // $locationDevices = DB::table(DB::raw("({$l}) as d"))->groupBy('device_id')->get();
        // $time4 = new Datetime('NOW');
        $endTime = '2020-11-10T05:44:39Z';
        $startTime = '2020-11-10T04:44:39Z';
        $time3 = new Datetime('NOW');
        $locationDynamo = Regular::where('datetime', 'between', [$startTime, $endTime])->where('lat', 'not_contains', '0.0')->limit(10000)->get();
        $locationDynamo->sortByDesc('datetime');
        $time4 = new Datetime('NOW');
        $onlineCount = 0;
        $offlineCount = 0;
        foreach ($devices as $device) {
            // $location = $locationDevices->where('device_id', '=', $device->device_id);
            $location = $locationDynamo->where('device','=',$device->device_id);
            if ($location->count() > 0) {
                // $device->latitude = $location->first()->latitude;
                // $device->longitude = $location->first()->longitude;
                $device->latitude = $location->first()->lat;
                $device->longitude = $location->first()->lng;
            }

            if ($device->type == 3) {
                $device->online = false;
                $offlineCount += 1;
            } else {
                $device->online = true;
                $onlineCount += true;
            }
        }



        $meta = ['online_count' => $onlineCount, 'offline_count' => $offlineCount];
        return ['data' => $devices, 'meta' => $meta];
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
