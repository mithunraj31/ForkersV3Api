<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Drive extends Model
{
    use HasFactory;
    protected $table = 'drive';

    public static function getDriveSummary($deviceId, $startTime, $endTime)
    {
        // declare types of regular
        $startEngine = 2;
        $stopEngine = 3;
        $normal = 1;
        $registerDriver = 4;
        $StartTime = date('h:i:sa');
        $regularData = DB::table('drive')->where('device_id', '=', $deviceId)->whereBetween('time', [$startTime, $endTime])->orderBy('time', 'asc')->get();
        $EndTime = date('h:i:sa');

        $first = 0;
        $second = 0;
        $driveDataArray = [];
        for ($j = 0; count($regularData) > $j; $j++) {
            $rData = $regularData[$j];
            $key = $j;
            // when data start whith Driver Change
            if ($key == 0 && $rData->type == $registerDriver) {
                $prefixData = DB::table('drive')->where([['device_id', '=', $deviceId], ['time', '<', $startTime], ['type', '=', $startEngine]])->orderBy('time', 'desc')->first();
                if ($prefixData) {
                    $tempEndTime = date('Y-m-d H:i:s', (strtotime($startTime) - 1));
                    $prefixDataArray = DB::table('drive')->where('device_id', '=', $deviceId)->whereBetween('time', [$prefixData->time, $tempEndTime])->orderBy('time', 'asc')->get();
                    $rData = $prefixData;
                    for ($i = $key; $i < count($regularData) && $regularData[$i]->type != 4; $i++) {
                        unset($regularData[$i]);
                    }
                    $regularData = $prefixDataArray->merge($regularData);
                }
            }
            // when engine start is before the context
            if ($key == 0 && $rData->type == 3) {
                $driveData = [
                    'engine_started_at' => null,
                    'engine_stoped_at' => $rData->time,
                ];
                $prefixData = DB::table('drive')->where([['device_id', '=', $deviceId], ['time', '<', $startTime], ['type', '=', $startEngine]])->orderBy('time', 'desc')->first();
                if ($prefixData) {
                    $tempEndTime = date('Y-m-d H:i:s', (strtotime($startTime) - 1));
                    $prefixDataArray = DB::table('drive')->where('device_id', '=', $deviceId)->whereBetween('time', [$prefixData->time, $tempEndTime])->orderBy('time', 'asc')->get();
                    $rData = $prefixData;
                    $regularData = $prefixDataArray->merge($regularData);
                } else {
                    array_push($driveDataArray, $driveData);
                }
            }
            // when engine off is after the context
            if ($rData->type == 2) {
                $isInContext = false;
                if ($key != count($regularData) - 1)
                    for ($i = $key + 1; count($regularData) - 1 > $i; $i++) {
                        if ($regularData[$i]->type == 3) {
                            $isInContext = true;
                        }
                    }
                if (!$isInContext) {
                    $c = count($regularData);
                    for ($i = $key + 1; $c > $i; $i++) {
                        unset($regularData[$i]);
                    }
                    $driveData = [
                        'engine_started_at' => $rData->time,
                        'engine_stoped_at' => null
                    ];
                    $postfixData = DB::table('drive')->where([['device_id', '=', $deviceId], ['time', '>', $endTime], ['type', '=', $stopEngine]])->orderBy('time', 'asc')->first();;
                    if ($postfixData) {
                        $tempStartTime = date('Y-m-d H:i:s', (strtotime($rData->time) + 1));
                        $tempEndTime = $postfixData->time;
                        $postfixDataArray = DB::table('drive')->where('device_id', '=', $deviceId)->whereBetween('time', [$tempStartTime, $tempEndTime])->orderBy('time', 'asc')->get();
                        $regularData = $regularData->merge($postfixDataArray);
                    } else {

                        array_push($driveDataArray, $driveData);
                    }
                }
            }
            // when engine on and off are in context
            if ($key != (count($regularData) - 1) && $rData->type == 2) {
                $driveData = [
                    'engine_started_at' => $rData->time,
                    'engine_stoped_at' => null
                ];
                $driverData = [];
                for ($i = $key; $i < count($regularData) && $regularData[$i]->type != 3; $i++) {
                    if (
                        $regularData[$i]->type == 4 ||
                        ($regularData[$i]->type == 2 && $regularData[$i]->driver_id != '')
                    ) {
                        if (count($driverData) > 0) {
                            $driverData[count($driverData) - 1]['drive_ended_at'] = $regularData[$i]->time;
                        }

                        $dd = [
                            'driver_id' => $regularData[$i]->driver_id,
                            'drive_start_at' => $regularData[$i]->time,
                            'drive_ended_at' => null
                        ];

                        if ($i != (count($regularData) - 1) && $regularData[$i + 1]->type == 3) {
                            $dd['drive_ended_at'] = $regularData[$i + 1]->time;
                            $driveData['engine_stoped_at'] = $regularData[$i + 1]->time;
                        }
                        array_push($driverData, $dd);
                    }
                }
                if ($key != (count($regularData) - 1) && $regularData[$key + 1]->type == 3) {
                    $driveData['engine_stoped_at'] = $regularData[$key + 1]->time;
                }
                $driveData['driver_data'] = $driverData;
                array_push($driveDataArray, $driveData);
            }
        }

        return $driveDataArray;
        // return $regularData;
    }

    public static function getDevices(){
        return DB::table('drive')->orderBy('time','desc')->groupBy('device_id')->get();
    }
}
