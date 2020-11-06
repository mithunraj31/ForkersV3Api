<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class Regular extends Model
{
    use HasFactory;

    protected $table = 'regular';

    protected $fillable = ['*'];

    public static function getDriveSummary($deviceId, $startTime,$endTime) {
        // declare types of regular
        $startEngine = 2;
        $stopEngine = 3;
        $normal = 1;
        $registerDriver = 4;
        $StartTime = date("h:i:sa");
        $regularData = DB::table('regular')->
        where('device_id','=',$deviceId)->
        whereBetween('time',[$startTime,$endTime])->
        // whereIn('type',[$startEngine,$stopEngine])->
        orderBy('time', 'asc')->
        get();
        $EndTime = date("h:i:sa");

        $first = 0;
        $second = 0;
        $driveDataArray =[];
        foreach($regularData as $key => $data){
            // when engine start is before the context
            if($key==0 && $data->type==3){

            }
            // when engine off is after the context
            if($key==(count($regularData)-1) && $data->type==2){

            }
            // when engine on and off are in context
            if($key!=(count($regularData)-1) && $data->type == 2 && $regularData[$key+1]->type==3){
                $driveData = [
                    "engine_started_at" => $data->time,
                    "engine_stoped_at" => $regularData[$key+1]->time
                ];
                array_push($driveDataArray, $driveData);
            }
        }

        return $driveDataArray;
    }
}
