<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\VehicleValidator;
use App\Http\Resources\VehicleResource;
use App\Http\Resources\VehicleResourceCollection;
use App\Models\Device;
use App\Models\DTOs\VehicleDto;
use App\Models\Operator;
use App\Models\Regular;
use App\Models\Vehicle;
use App\Models\VehicleDevice;
use App\Services\Interfaces\VehicleServiceInterface;
use BaoPham\DynamoDb\RawDynamoDbQuery;
use DateTime;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class VehicleService extends ServiceBase implements VehicleServiceInterface
{

    public function create(VehicleDto $request)
    {
        VehicleValidator::storeVehicleValidator($request);

        if($request->device_id){
            $device = Device::find($request->device_id);
            if($device->assigned){
                throw new InvalidArgumentException("Device is already assigned");
            }
        }
        $vehicle = new Vehicle();
        $vehicle->name = $request->name;
        $vehicle->group_id = $request->group_id;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->structural_method = $request->structural_method;
        $vehicle->power_type = $request->power_type;
        $vehicle->rated_load = $request->rated_load;
        $vehicle->fork_length = $request->fork_length;
        $vehicle->standard_lift = $request->standard_lift;
        $vehicle->maximum_lift = $request->maximum_lift;
        $vehicle->battery_voltage = $request->battery_voltage;
        $vehicle->battery_capacity = $request->battery_capacity;
        $vehicle->hour_meter_initial_value = $request->hour_meter_initial_value;
        $vehicle->operating_time = $request->operating_time;
        $vehicle->cumulative_uptime = $request->cumulative_uptime;
        if($request->introduction_date){
            $vehicle->introduction_date = $request->introduction_date;
        }
        $vehicle->contract = $request->contract;
        $vehicle->key_number = $request->key_number;
        $vehicle->installation_location = $request->installation_location;
        $vehicle->option1 = $request->option1;
        $vehicle->option2 = $request->option2;
        $vehicle->option3 = $request->option3;
        $vehicle->option4 = $request->option4;
        $vehicle->option5 = $request->option5;
        $vehicle->remarks = $request->remarks;
        $vehicle->model_id = $request->model_id;

        $vehicle->owner_id = Auth::user()->id;
        if($request->customer_id){
            $vehicle->customer_id = $request->customer_id;
        }else{
            $vehicle->customer_id = Auth::user()->customer_id;
        }
        $vehicle->save();
        // assign device
        if($request->device_id){
            $this->assignDevice($request, $vehicle);
        }
    }

    public function update(VehicleDto $request, Vehicle $vehicle)
    {
        VehicleValidator::updateVehicleValidator($request,$vehicle);

        if($request->name){
            $vehicle->name = $request->name;
        }
        if($request->group_id){
            $vehicle->group_id =$request->group_id;
        }
        // assign or unassigned device for requested vehicle
        if($request->device_id){
            $this->assignDevice($request,$vehicle);
        }
        $vehicle->owner_id = Auth::user()->id;
        return $vehicle->save();

    }

    public function findById(Vehicle $vehicle)
    {
        VehicleValidator::findByIdValidator($vehicle);
        return new VehicleResource($vehicle->load('device'));
    }

    public function getAll($perPage=15)
    {
        if(AuthValidator::isAdmin()) {
            $paginator = Vehicle::with('device','customer', 'location')->paginate($perPage);
            $paginator->getCollection()->transform(function($value){
                return [
                    'id' =>$value->id,
                    'name' =>$value->name,
                    'owner_id'=> $value->owner_id,
                    'group_id'=> $value->group_id,
                    'customer_id'=> $value->customer_id,
                    'created_at'=>$value->created_at,
                    'updated_at'=>$value->updated_at,
                    'device' =>$value->device?$value->device->device:null,
                    'customer'=>$value->customer,
                    'location'=>$value->location?[
                        "latitude"=> $value->location->latitude,
                        "longitude"=> $value->location->longitude,
                        'created_at'=>$value->location->created_at,
                        'updated_at'=>$value->location->updated_at,
                    ]:null,
                    'is_online'=> $value->location?($value->location->type==3?false:true):false

                ];
             });
            return new VehicleResourceCollection($paginator);
        }else{
            $paginator = Vehicle::whereIn('group_id',AuthValidator::getGroups())->with('device','customer','location')->paginate($perPage);
            $paginator->getCollection()->transform(function($value){
                return [
                    'id' =>$value->id,
                    'name' =>$value->name,
                    'owner_id'=> $value->owner_id,
                    'group_id'=> $value->group_id,
                    'customer_id'=> $value->customer_id,
                    'created_at'=>$value->created_at,
                    'updated_at'=>$value->updated_at,
                    'device' =>$value->device?$value->device->device:null,
                    'customer'=>$value->customer,
                    'location'=>$value->location?[
                        "latitude"=> $value->location->latitude,
                        "longitude"=> $value->location->longitude,
                        'created_at'=>$value->location->created_at,
                        'updated_at'=>$value->location->updated_at,
                    ]:null,
                    'is_online'=> $value->location?($value->location->type==3?false:true):false

                ];
             });
            return new VehicleResourceCollection($paginator);
        }
    }

    public function delete(Vehicle $vehicle)
    {
        VehicleValidator::deleteVehicleValidator($vehicle);
        return $vehicle->delete();
    }

    private function assignDevice(VehicleDto $request, Vehicle $vehicle)
    {
        $requestedDevice = Device::find($request->device_id);
        if($requestedDevice->assigned){
            throw new InvalidArgumentException("Device is already assigned");
        }

        $vehicleDevice = new VehicleDevice();
        $vehicleDevice->vehicle_id = $vehicle->id;
        if($request->device_id === 'null'){
            $vehicleDevice->device_id = null;

        }else{
            $vehicleDevice->device_id = $request->device_id;

        }
        // unassigned previous device
        $device = $vehicle->device;
        if($device && $device->id){
            $deviceP = Device::find($device->id);
            $deviceP->assigned = false;
            $deviceP->save();

            // add null record to vehicle_device tabble
            $vehicleDevice = new VehicleDevice();
            $vehicleDevice->vehicle_id = null;
            $vehicleDevice->device_id = $device->id;
            $vehicleDevice->owner_id = Auth::user()->id;
            $vehicleDevice->save();
        }

        // assign current device
        $deviceC = Device::find($request->device_id);
        $deviceC->assigned = true;
        $deviceC->save();

        $vehicleDevice->owner_id = Auth::user()->id;
        $vehicleDevice->save();
    }
    public function getDriveSummary($vehicleId, $startTime, $endTime)
    {
        $drives = $this->getDriveSummaryByDynamo($vehicleId, $startTime, $endTime);
        return $drives;
    }

    public function getRoute($vehicleId, $start, $end)
    {
        $regular = new Regular();
        $startDate = $start;
        $endDate = $end;
        $data = $regular->where('vehicle_id', $vehicleId)->where('lat', 'not_contains', '0.000000')->where('datetime', 'between', [$startDate, $endDate])->limit(10000)->get();
        return ['data' => $data];
    }
    private function getDriveSummaryByDynamo($vehicleId, $startTime, $endTime)
    {
        // declare types of regular
        $startEngine = 2;
        $stopEngine = 3;
        $registerDriver = 4;
        // get regular data
        // $startDate = $this->formatDate($startTime);
        // $endDate = $this->formatDate($endTime);
        $regularData = $this->getRegularByTimeRange($vehicleId, $startTime, $endTime);
        if ($regularData->count() > 0) {
            // when start with not engine start
            if ($regularData[0]->type != $startEngine) {
                //get first start engine from dynamo
                $tempQueryStartDate = $regularData[0]->datetime;
                $startData = $this->getRegularStart($vehicleId, $tempQueryStartDate);
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

                    $prefixData =  $this->getRegularByTimeRange($vehicleId, $tempQueryStartDate, $tempQueryStopDate);
                    // concat new data
                    $regularData = $this->concatCollection($prefixData, $regularData);
                }
            }
            // when end is not Engine Stop
            if ($regularData[$regularData->count() - 1]->type != $stopEngine) {
                // get last stop engine from dynamo
                $tempQueryStopDate = $regularData[$regularData->count() - 1]->datetime;
                $stopData = $this->getRegularEnd($vehicleId, $tempQueryStopDate);

                if ($stopData->count() > 0) {
                    // remove Garbage values
                    $tempQueryStartDate = $regularData[$regularData->count() - 1]->datetime;
                    $tempQueryStopDate = $stopData[0]->datetime;

                    // get all post data
                    $postfixData = $this->getRegularByTimeRange($vehicleId, $this->addFormatDate($tempQueryStartDate,1), $tempQueryStopDate);
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
    public function getRegularByTimeRange($vehicleId, $startTime, $endTime)
    {
        return Regular::where(['vehicle_id' => $vehicleId])
            ->where('datetime', 'between', [$startTime, $endTime])
            ->where('type', '!=', '1')
            ->limit(10000)->get();
    }
    private function getRegularStart($vehicleId, $time)
    {
        return Regular::where(['vehicle_id' => $vehicleId])
            ->where('datetime', '<', $time)
            ->where('type', '2')
            ->decorate(function (RawDynamoDbQuery $raw) {
                // desc order
                $raw->query['ScanIndexForward'] = false;
            })
            ->limit(1)->get();
    }
    private function getRegularEnd($vehicleId, $time)
    {
        return Regular::where(['vehicle_id' => $vehicleId])
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
                $engineStart = new DateTime($regular->datetime);
                $driveData = [
                    'engine_started_at' => $engineStart->format('Y-m-d H:i:s'),
                    'engine_stoped_at' => null
                ];
                $operatorData = [];

                foreach ($regularData as $j => $subRegular) {
                    if ($j >= $i) {
                        $dd = [
                            'operator' => null,
                            'drive_start_at' => null,
                            'drive_ended_at' => null
                        ];
                        if (($subRegular->type == 2 || $subRegular->type == 4) && $subRegular->operator_id != "unassigned") {
                            $dd = [
                                'operator' => Operator::find($subRegular->operator_id),
                                'drive_start_at' => $subRegular->datetime,
                                'drive_ended_at' => null
                            ];
                            if (count($operatorData) > 0) {
                                $operatorData[count($operatorData) - 1]['drive_ended_at'] = $subRegular->datetime;
                            }
                            array_push($operatorData, $dd);
                        }
                        if ($subRegular->type == 3) {
                            if (count($operatorData) > 0) {
                                $operatorData[count($operatorData) - 1]['drive_ended_at'] = $subRegular->datetime;
                            }
                            $driveData['engine_stoped_at'] = $subRegular->datetime;
                            $driveData['operator_data'] = $operatorData;
                            array_push($driveSummary, $driveData);
                            break;
                        }
                        // last data and end is not found
                        if ($j == ($regularData->count() - 1) && $subRegular->type != 3) {
                            $driveData['operator_data'] = $operatorData;
                            array_push($driveSummary, $driveData);
                        }
                    }
                }
            }
        }
        return $driveSummary;
    }

    private function addFormatDate($date,$seconds)
    {
        $newDate = new DateTime($date);
        $newDate->modify("+1$seconds second");
        $dateStr = $newDate->format('Y-m-d H:i:s');
        // $dateStr = str_replace(' ', 'T', $dateStr) . 'Z';
        return $dateStr;
    }
}
