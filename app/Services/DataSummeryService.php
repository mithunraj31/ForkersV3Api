<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Operator;
use App\Models\OperatorStat;
use App\Services\Interfaces\StonkamServiceInterface;

use App\Services\Interfaces\DataSummeryServiceInterface;
use Illuminate\Support\Facades\Date;

class DataSummeryService extends ServiceBase implements DataSummeryServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }
    public function getEventsByOperators($start, $end, $operator_ids)
    {
        $operators = Operator::find($operator_ids);
        $summery = collect([]);
        // initialize summery collection
        foreach($operators as $operator){
            $summery->put($operator->id,
            [
                'operator' => $operator,
                'event_summery' => [
                    'handle_left' => 0,
                    'handle_right' =>  0,
                    'acceleration' =>  0,
                    'deacceleration'  =>  0,
                    'accident' =>  0,
                    'button' => 0
                ],
                'running_time' => 0
            ]);
        }
        // calculate count of events
        $summery = $this->getEventCountByOperators($summery,$operator_ids,$start,$end);
        // calculate driving time
        $summery = $this->calculateOperatorRunningTime($summery,$start,$end,$operator_ids);

        return $this->collectionToArray($summery);
    }

    private function getEventCountByOperators($summery,$operator_ids,$start, $end){
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;

        $events = Event::whereIn('operator_id',$operator_ids)->whereBetween('time',[$start,$end])->get();
        foreach($summery as $key => $summ){
            $accelerateCount = $events->where('operator_id',$key)->where('type',$accelerate)->count();
            $decelerateCount = $events->where('operator_id',$key)->where('type',$decelerate)->count();
            $impactCount = $events->where('operator_id',$key)->where('type',$impact)->count();
            $turnLeftCount = $events->where('operator_id',$key)->where('type',$turnLeft)->count();
            $turnRightCount = $events->where('operator_id',$key)->where('type',$turnRight)->count();
            $buttonCount = $events->where('operator_id',$key)->where('type',$button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summery->put($key,$summ);
        }
        return $summery;

    }
    private function calculateOperatorRunningTime($summery,$start, $end, $operator_ids){
        $dailyOperatorDurations = OperatorStat::whereIn('operator_id',$operator_ids)->whereBetween('date',[$start,$end])->get();
        foreach($summery as $key => $summ){
            $sumOfDuration = $dailyOperatorDurations->where('operator_id',$key)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key,$summ);
        }
        return $summery;
    }

    private function collectionToArray($collection)
    {
        $array = [];
        foreach($collection as $key => $value){
            array_push($array,$value);
        }
        return $array;
    }


}
