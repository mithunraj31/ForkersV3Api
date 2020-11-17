<?php

namespace App\Services;

use App\Models\Drive;
use App\Services\Interfaces\OperatorServiceInterface;
use Illuminate\Support\Facades\DB;

class OperatorService implements OperatorServiceInterface
{
    public function getDriveSummery($operatorId, $startTime, $endTime)
    {
        // declare types of regular
        $startEngine = 2;
        $stopEngine = 3;
        $normal = 1;
        $registerDriver = 4;
        $regularData = DB::table('drive')->whereBetween('time', [$startTime, $endTime])->orderBy('time', 'asc')->get();

        $driveDataArray = [];
        for ($j = 0; count($regularData) > $j; $j++) {
            $rData = $regularData[$j];
            $key = $j;

            // when engine start is before the context
            if ($key == 0 && $rData->type == $stopEngine && $rData->driver_id == $operatorId) {
                $driveData = [
                    "drive_started_at" => null,
                    "drive_stoped_at" => $rData->time,
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
            if ($rData->type == $registerDriver && $rData->driver_id == $operatorId && $key == count($regularData) - 1) {
                $driveData = [
                    "drive_started_at" => $rData->time,
                    "drive_stoped_at" => null
                ];
                $postfixData = DB::table('drive')->where([['device_id', '=', $operatorId], ['time', '>', $endTime]])->orderBy('time', 'asc')->first();;
                if ($postfixData) {
                    array_push($regularData, $postfixData);
                } else {

                    array_push($driveDataArray, $driveData);
                }
            }
            // when engine on and off are in context
            if ($key != (count($regularData) - 1) && $rData->type == $registerDriver && $rData->driver_id == $operatorId) {
                $driveData = [
                    "drive_started_at" => $rData->time,
                    "drive_stoped_at" => null
                ];
                $driverData = [];

                $driveData['drive_stoped_at'] = $regularData[$key + 1]->time;
                array_push($driveDataArray, $driveData);
            }
        }

        return $driveDataArray;
        // return $regularData;
    }
}
