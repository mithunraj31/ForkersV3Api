<?php

namespace App\Console\Commands;

use App\Models\OperatorStat;
use App\Models\Vehicle;
use App\Models\VehicleStat;
use App\Services\Interfaces\VehicleServiceInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;

class GenerateDailyStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyStat:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily vehicle and operator stats';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private VehicleServiceInterface $vehicleService;
    public function __construct(
        VehicleServiceInterface $vehicleService
    ) {
        parent::__construct();
        $this->vehicleService = $vehicleService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // VehicleStat::generateDailyStat();
        // logger()->info('Daily Stats generated!');
        $today = Carbon::today('Asia/Tokyo');
        $todayStr = $today->format('Y-m-d');
        $yesterday = Carbon::yesterday('Asia/Tokyo');
        $today->setTimezone('UTC');
        $yesterday->setTimezone('UTC');
        $today = $today->format('Y-m-d H:i:s');
        $yesterday = $yesterday->format('Y-m-d H:i:s');
        logger()->info("Generating daily stat from  $yesterday  to $today");


        ////////////////////////////////////////////
        //////////temp//////////
        // $today = '2021-01-18 05:04:20';
        // $yesterday = '2021-01-18 00:00:00';
        ////////////////////////////////////////////


        //get vehicle list
        $vehicles = Vehicle::select('id')->get();
        logger()->info("vehicles list fetched");
        $vehicleDuration = collect([]);
        $operatorDuration = collect([]);

        // iterating through vehicles
        foreach ($vehicles as $vehicle) {
            $regularData = $this->vehicleService->getRegularByTimeRange(strval($vehicle->id), $yesterday, $today);
            // make starting record as start engine
            if ($regularData && ($regularData[0]->type == 1 || $regularData[0]->type == 4)) {
                $regularData[0]->type = '2';
            }
            // // make ending record as end engine
            if ($regularData && $regularData[count($regularData) - 1]->type != 3) {
                $regularData[count($regularData) - 1]->type = '3';
            }


            // calculate duration of vehicle
            //initialize vehicle duration
            $vehicleDuration->put($vehicle->id, 0);
            $driveSummary = $this->generateDriveSummery($regularData);

            foreach ($driveSummary as $drive) {
                //addition of drive data
                $start = Carbon::create($drive['engine_started_at']);
                $end = Carbon::create($drive['engine_stoped_at']);
                $vehicleDiff = $end->diffInSeconds($start); //in seconds
                $vehicleDuration->put($vehicle->id, $vehicleDuration->get($vehicle->id) + $vehicleDiff);

                //addition of operator data
                if ($drive['operator_data']) {
                    foreach ($drive['operator_data'] as $operator) {

                        //initialize operator is not exists
                        if (!$operatorDuration->get($operator['operator'])) {
                            $operatorDuration->put($operator['operator'], 0);
                        }
                        $startOperator = Carbon::create($operator['drive_start_at']);
                        $endOperator = Carbon::create($operator['drive_ended_at']);
                        $operatorDiff = $endOperator->diffInSeconds($startOperator); //in seconds
                        $operatorDuration->put($operator['operator'], $operatorDuration->get($operator['operator']) + $operatorDiff);
                    }
                }
            }
        }
        $this->insertDriveStat($vehicleDuration, $todayStr);
        $this->insertOperatorStat($operatorDuration, $todayStr);
    }

    private function generateDriveSummery($regularData)
    {
        $driveSummary = [];
        $i = 0;
        foreach ($regularData as $i => $regular) {
            //Start engine
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
                                'operator' => $subRegular->operator_id,
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

    private function insertDriveStat($driveDuration, $date)
    {
        $data = [];
        foreach ($driveDuration as $key => $value) {
            $row = [
                "vehicle_id" => $key,
                "duration" => $value,
                "date" => $date,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            array_push($data, $row);
        }
        print_r($data);
        if (count($data) > 0) {
            VehicleStat::insert($data);
        }
    }
    private function insertOperatorStat($operatorDuration, $date)
    {
        $data = [];
        foreach ($operatorDuration as $key => $value) {
            $row = [
                "operator_id" => $key,
                "duration" => $value,
                "date" => $date,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            array_push($data, $row);
        }
        print_r($data);
        if (count($data) > 0) {
            OperatorStat::insert($data);
        }
    }
}
