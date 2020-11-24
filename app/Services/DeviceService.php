<?php

namespace App\Services;

use App\Models\Drive;
use App\Models\Regular;
use App\Services\Interfaces\DeviceServiceInterface;
use DateTime;

class DeviceService implements DeviceServiceInterface
{
    public function getAllDevice()
    {
        return null;
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
        $data = $regular->where('device',$deviceId)->
                 where('lat','not_contains','0.0')->
                 where('datetime', 'between',[$startDate,$endDate])->
                 limit(10000)->
                 get();
        return ['data' => $data];
    }

    private function formatDate($date){
        $newDate = new DateTime($date);
        $dateStr = $newDate->format('Y-m-d H:i:s');
        $dateStr = str_replace(' ','T',$dateStr).'Z';
        return $dateStr;
    }
}
