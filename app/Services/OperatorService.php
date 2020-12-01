<?php

namespace App\Services;

use App\Services\Interfaces\OperatorServiceInterface;
use Illuminate\Support\Facades\DB;

class OperatorService extends ServiceBase implements OperatorServiceInterface
{
    public function getDriveSummery($operatorId, $startTime, $endTime)
    {
        // declare types of regular
        $stopEngine = 3;
        $registerDriver = 4;
        $regularData = DB::table('drive')->whereBetween('time', [$startTime, $endTime])->orderBy('time', 'asc')->get();

        $driveDataArray = [];
        for ($j = 0; count($regularData) > $j; $j++) {
            $rData = $regularData[$j];
            $key = $j;

            // when engine start is before the context
            if ($key == 0 && $rData->type == $stopEngine && $rData->driver_id == $operatorId) {
                $driveData = [
                    'drive_started_at' => null,
                    'drive_stoped_at' => $rData->time,
                ];
                $prefixData = DB::table('drive')->where([['device_id', '=', $operatorId], ['time', '<', $startTime], ['type', '=', $registerDriver]])->orderBy('time', 'desc')->first();
                if ($prefixData) {

                    $rData = $prefixData;
                    array_unshift($regularData, $prefixData);
                } else {
                    array_push($driveDataArray, $driveData);
                }
            }
            // when engine off is after the context
            if ($rData->type == $registerDriver && $rData->driver_id == $operatorId) {
                $isAfterContext = true;
                if ($key == (count($regularData) - 1)) {
                    $isAfterContext = true;
                } else {

                    for ($j = $key + 1; $j < count($regularData); $j++) {
                        if ($regularData[$j]->driver_id == $operatorId) {
                            $isAfterContext = false;
                            break;
                        }
                    }
                }

                if ($isAfterContext) {
                    $driveData = [
                        'drive_started_at' => $rData->time,
                        'drive_stoped_at' => null
                    ];
                    $postfixData = DB::table('drive')->where([['driver_id', '=', $operatorId], ['time', '>', $endTime]])->orderBy('time', 'asc')->first();;
                    if ($postfixData) {
                        $regularData->push($postfixData);
                    } else {

                        array_push($driveDataArray, $driveData);
                    }
                }
            }
            // when engine on and off are in context
            if ($key != (count($regularData) - 1) &&
            ($rData->type == $registerDriver || $rData->driver_id != '')&&
            $rData->driver_id == $operatorId) {
                $driveData = [
                    'drive_started_at' => $rData->time,
                    'drive_stoped_at' => null,
                    'device_id' => $rData->device_id
                ];

                for ($i = $key + 1; $i < count($regularData); $i++) {
                    if ($regularData[$i]->driver_id == $operatorId) {
                        $driveData['drive_stoped_at'] = $regularData[$i]->time;
                        break;
                    }
                }
                array_push($driveDataArray, $driveData);
            }
        }
        $duration = $this->calculateDriveDuration($driveDataArray);
        return ['data'=>$driveDataArray, 'duration'=>$duration];
        // return $regularData;
    }

    public function getOperatorEvents($operatorId, $start, $end)
    {
        return DB::table('event')->where([['driver_id', '=', $operatorId]])->whereBetween('time', [$start, $end])->orderBy('time', 'desc')->get();
    }

    private function calculateDriveDuration($drives)
    {
        if ($drives && count($drives) > 0) {
            $duration = 0;
            foreach ($drives as $key => $drive) {
                if ($drive['drive_started_at']&& $drive['drive_stoped_at']) {
                    $start = strtotime($drive['drive_started_at']);
                    $end = strtotime($drive['drive_stoped_at']);
                    $d = $end-$start;
                    $duration += $d;
                 }
            }
            return $duration;
        } else {
            return 0;
        }
    }
}
