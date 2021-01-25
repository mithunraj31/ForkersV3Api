<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Group;
use App\Models\Operator;
use App\Models\OperatorStat;
use App\Models\Vehicle;
use App\Models\VehicleStat;
use App\Services\Interfaces\StonkamServiceInterface;

use App\Services\Interfaces\DataSummeryServiceInterface;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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
        foreach ($operators as $operator) {
            $summery->put(
                $operator->id,
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
                ]
            );
        }
        // calculate count of events
        $summery = $this->getEventCountByOperators($summery, $operator_ids, $start, $end);
        // calculate driving time
        $summery = $this->calculateOperatorRunningTime($summery, $start, $end, $operator_ids);

        return $this->collectionToArray($summery);
    }
    public function getEventsByVehicles($start, $end, $vehicle_ids)
    {
        $vehicles = Vehicle::find($vehicle_ids);
        $summery = collect([]);
        // initialize summery collection
        foreach ($vehicles as $vehicle) {
            $summery->put(
                $vehicle->id,
                [
                    'vehicle' => $vehicle,
                    'event_summery' => [
                        'handle_left' => 0,
                        'handle_right' =>  0,
                        'acceleration' =>  0,
                        'deacceleration'  =>  0,
                        'accident' =>  0,
                        'button' => 0
                    ],
                    'running_time' => 0
                ]
            );
        }
        // calculate count of events
        $summery = $this->getEventCountByVehicles($summery, $vehicle_ids, $start, $end);
        // calculate driving time
        $summery = $this->calculateVehicleRunningTime($summery, $start, $end, $vehicle_ids);

        return $this->collectionToArray($summery);
    }
    public function getEventsByGroups($start, $end, $groups_ids)
    {
        $groups = Group::find($groups_ids);
        $summery = collect([]);
        // initialize summery collection
        foreach ($groups as $group) {
            $summery->put(
                $group->id,
                [
                    'group' => $group,
                    'event_summery' => [
                        'handle_left' => 0,
                        'handle_right' =>  0,
                        'acceleration' =>  0,
                        'deacceleration'  =>  0,
                        'accident' =>  0,
                        'button' => 0
                    ],
                    'running_time' => 0
                ]
            );
        }
        // get vehicles of groups
        $vehicles = Vehicle::select('id', 'group_id')->whereIn('group_id', $groups_ids)->get();
        // calculate count of events
        $summery = $this->getEventCountByGroups($summery, $start, $end, $vehicles);
        // calculate driving time
        $summery = $this->calculateGroupRunningTime($summery, $start, $end, $vehicles);

        return $this->collectionToArray($summery);
    }
    public function getAlarmsByAllOperators($start, $end, $customerId = null)
    {
        // get customer
        if (!$customerId) {
            $customerId = Auth::user()->customer_id;
        }
        // generate period
        $dayArray = $this->generateDateRange($start, $end);

        $summery = collect([]);
        // initialize summery collection
        foreach ($dayArray as $day) {
            $summery->put(
                $day,
                [
                    'summary_date' => $day,
                    'event_summery' => [
                        'handle_left' => 0,
                        'handle_right' =>  0,
                        'acceleration' =>  0,
                        'deacceleration'  =>  0,
                        'accident' =>  0,
                        'button' => 0
                    ],
                    'running_time' => 0
                ]
            );
        }
        //get all operators of customer
        $operators = Operator::select('id')->where('customer_id',$customerId)->get();
        $operator_ids = $this->getIdArray($operators);

        // calculate count of events
        $summery = $this->getAlarmCountByAllOperators($summery, $start, $end, $operator_ids);
        // calculate driving time
        $summery = $this->calculateAllOperatorsRunningTime($summery, $start, $end, $operator_ids);

        return $this->collectionToArray($summery);

        return $summery;
    }
    public function getAlarmsByAllVehicles($start, $end, $customerId = null)
    {
        // get customer
        if (!$customerId) {
            $customerId = Auth::user()->customer_id;
        }
        // generate period
        $dayArray = $this->generateDateRange($start, $end);

        $summery = collect([]);
        // initialize summery collection
        foreach ($dayArray as $day) {
            $summery->put(
                $day,
                [
                    'summary_date' => $day,
                    'event_summery' => [
                        'handle_left' => 0,
                        'handle_right' =>  0,
                        'acceleration' =>  0,
                        'deacceleration'  =>  0,
                        'accident' =>  0,
                        'button' => 0
                    ],
                    'running_time' => 0
                ]
            );
        }
        //get all vehicles of customer
        $vehicles = Vehicle::select('id')->where('customer_id',$customerId)->get();
        $vehicle_ids = $this->getIdArray($vehicles);

        // calculate count of events
        $summery = $this->getAlarmCountByAllVehicles($summery, $start, $end, $vehicle_ids);
        // calculate driving time
        $summery = $this->calculateAllVehilcesRunningTime($summery, $start, $end, $vehicle_ids);

        return $this->collectionToArray($summery);

        return $summery;
    }
    private function getAlarmCountByAllVehicles($summery, $start, $end, $vehicle_ids)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;
        $start = $this->getUTCtime($start);
        $end = $this->getUTCtime($end);
        $events = Event::whereIn('vehicle_id', $vehicle_ids)->whereBetween('time', [$start, $end])->get();

        foreach ($summery as $key => $summ) { // key is in localtimezone
            $day = $this->generateDayRange($key);

            $accelerateCount = $events->where('type', $accelerate)->whereBetween('time', [$day['startUTC'],$day['endUTC']])->count();
            $decelerateCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $decelerate)->count();
            $impactCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $impact)->count();
            $turnLeftCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $turnLeft)->count();
            $turnRightCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $turnRight)->count();
            $buttonCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function calculateAllVehilcesRunningTime($summery,$start, $end, $vehicle_ids){ // start and end are in locale datetime

        $dailyVehicleDurations = VehicleStat::whereIn('vehicle_id', $vehicle_ids)->whereBetween('date', [$start, $end])->get();
        foreach ($summery as $key => $summ) { // key is localdatetime
            $sumOfDuration = $dailyVehicleDurations->where('date', $key)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function getAlarmCountByAllOperators($summery, $start, $end, $operator_ids)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;
        $start = $this->getUTCtime($start);
        $end = $this->getUTCtime($end);
        $events = Event::whereIn('operator_id', $operator_ids)->whereBetween('time', [$start, $end])->get();

        foreach ($summery as $key => $summ) { // key is in localtimezone
            $day = $this->generateDayRange($key);

            $accelerateCount = $events->where('type', $accelerate)->whereBetween('time', [$day['startUTC'],$day['endUTC']])->count();
            $decelerateCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $decelerate)->count();
            $impactCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $impact)->count();
            $turnLeftCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $turnLeft)->count();
            $turnRightCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $turnRight)->count();
            $buttonCount = $events->whereBetween('time', [$day['startUTC'],$day['endUTC']])->where('type', $button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function calculateAllOperatorsRunningTime($summery,$start, $end, $operator_ids){ // start and end are in locale datetime

        $dailyOperatorDurations = OperatorStat::whereIn('operator_id', $operator_ids)->whereBetween('date', [$start, $end])->get();
        foreach ($summery as $key => $summ) { // key is localdatetime
            $sumOfDuration = $dailyOperatorDurations->where('date', $key)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function getEventCountByGroups($summery, $start, $end, $vehicles)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;
        $vehicle_ids = $this->getIdArray($vehicles);
        $events = Event::whereIn('vehicle_id', $vehicle_ids)->whereBetween('time', [$start, $end])->get();
        foreach ($summery as $key => $summ) {
            $vehiclesOfGroup = $vehicles->where('group_id', $key);
            $vehicle_idsOfGroup = $this->getIdArray($vehiclesOfGroup);
            $vehicleCount = $vehiclesOfGroup->count();

            $accelerateCount = $events->whereIn('vehicle_id', $vehicle_idsOfGroup)->where('type', $accelerate)->count();
            $decelerateCount = $events->whereIn('vehicle_id', $vehicle_idsOfGroup)->where('type', $decelerate)->count();
            $impactCount = $events->whereIn('vehicle_id', $vehicle_idsOfGroup)->where('type', $impact)->count();
            $turnLeftCount = $events->whereIn('vehicle_id', $vehicle_idsOfGroup)->where('type', $turnLeft)->count();
            $turnRightCount = $events->whereIn('vehicle_id', $vehicle_idsOfGroup)->where('type', $turnRight)->count();
            $buttonCount = $events->whereIn('operator_id', $vehicle_idsOfGroup)->where('type', $button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summ['group']['number_of_vehicles'] = $vehicleCount;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function calculateGroupRunningTime($summery, $start, $end, $vehicles)
    {
        $vehicle_ids = $this->getIdArray($vehicles);
        $dailyVehicleDurations = VehicleStat::whereIn('vehicle_id', $vehicle_ids)->whereBetween('date', [$start, $end])->get();
        foreach ($summery as $key => $summ) { // $key is group_id
            $vehicle_idsOfGroup = $this->getIdArray($vehicles->where('group_id', $key));
            $sumOfDuration = $dailyVehicleDurations->whereIn('vehicle_id', $vehicle_idsOfGroup)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function getEventCountByVehicles($summery, $vehicle_ids, $start, $end)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;

        $events = Event::whereIn('vehicle_id', $vehicle_ids)->whereBetween('time', [$start, $end])->get();
        foreach ($summery as $key => $summ) {
            $accelerateCount = $events->where('vehicle_id', $key)->where('type', $accelerate)->count();
            $decelerateCount = $events->where('vehicle_id', $key)->where('type', $decelerate)->count();
            $impactCount = $events->where('vehicle_id', $key)->where('type', $impact)->count();
            $turnLeftCount = $events->where('vehicle_id', $key)->where('type', $turnLeft)->count();
            $turnRightCount = $events->where('vehicle_id', $key)->where('type', $turnRight)->count();
            $buttonCount = $events->where('operator_id', $key)->where('type', $button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summery->put($key, $summ);
        }
        return $summery;
    }

    private function calculateVehicleRunningTime($summery, $start, $end, $vehicle_ids)
    {
        $dailyOperatorDurations = VehicleStat::whereIn('vehicle_id', $vehicle_ids)->whereBetween('date', [$start, $end])->get();
        foreach ($summery as $key => $summ) {
            $sumOfDuration = $dailyOperatorDurations->where('vehicle_id', $key)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key, $summ);
        }
        return $summery;
    }

    private function getEventCountByOperators($summery, $operator_ids, $start, $end)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $impact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;

        $events = Event::whereIn('operator_id', $operator_ids)->whereBetween('time', [$start, $end])->get();
        foreach ($summery as $key => $summ) {
            $accelerateCount = $events->where('operator_id', $key)->where('type', $accelerate)->count();
            $decelerateCount = $events->where('operator_id', $key)->where('type', $decelerate)->count();
            $impactCount = $events->where('operator_id', $key)->where('type', $impact)->count();
            $turnLeftCount = $events->where('operator_id', $key)->where('type', $turnLeft)->count();
            $turnRightCount = $events->where('operator_id', $key)->where('type', $turnRight)->count();
            $buttonCount = $events->where('operator_id', $key)->where('type', $button)->count();

            $summ['event_summery']['acceleration'] = $accelerateCount;
            $summ['event_summery']['deacceleration'] = $decelerateCount;
            $summ['event_summery']['handle_left'] = $turnLeftCount;
            $summ['event_summery']['handle_right'] = $turnRightCount;
            $summ['event_summery']['accident'] = $impactCount;
            $summ['event_summery']['button'] = $buttonCount;
            $summery->put($key, $summ);
        }
        return $summery;
    }
    private function calculateOperatorRunningTime($summery, $start, $end, $operator_ids)
    {
        $dailyOperatorDurations = OperatorStat::whereIn('operator_id', $operator_ids)->whereBetween('date', [$start, $end])->get();
        foreach ($summery as $key => $summ) {
            $sumOfDuration = $dailyOperatorDurations->where('operator_id', $key)->sum('duration');
            $summ['running_time'] = $sumOfDuration;
            $summery->put($key, $summ);
        }
        return $summery;
    }

    private function collectionToArray($collection)
    {
        $array = [];
        foreach ($collection as $key => $value) {
            array_push($array, $value);
        }
        return $array;
    }

    private function getIdArray($collection)
    {
        $array = [];
        foreach ($collection as $value) {
            array_push($array, $value['id']);
        }
        return $array;
    }

    private function generateDateRange($start, $end)
    {
        // generate period
        $dayArray = [];
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $day) {
            array_push($dayArray, $day->format(('Y-m-d')));
        }
        return $dayArray;
    }

    private function generateDayRange($date){
        $start = Carbon::create($date,'Asia/Tokyo');
            $end = Carbon::create($date,'Asia/Tokyo');
            $end = $end->add(1,'day');

            $startUTC = $start->setTimezone('UTC');
            $endUTC = $end->setTimezone('UTC');

            $startUTC = $start->format('Y-m-d H:i:s');
            $endUTC = $end->format('Y-m-d H:i:s');
            return [
                'startUTC' => $startUTC,
                'endUTC' =>$endUTC
            ];
    }
    private function getUTCtime($date){
        $local = Carbon::create($date,'Asia/Tokyo');
        $local = $local->setTimezone('UTC');
        return $local->format('Y-m-d H:i:s');
    }
}
