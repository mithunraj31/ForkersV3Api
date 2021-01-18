<?php

namespace App\Services;

use App\Models\Charts;
use App\Models\DTOs\ChartsDto;
use App\Services\Interfaces\ChartServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ChartService extends ServiceBase implements ChartServiceInterface
{
    public function create(ChartsDto $model)
    {
        Log::info('Creating chart', $model->toArray());
        $chart = new Charts();
        $chart->name = $model->name;
        $chart->type = $model->type;
        $chart->api_path = $model->apiPath;
        $chart->is_private = $model->isPrivate;
        $chart->owner_id = $model->ownerId;
        $chart->customer_id = $model->customerId;
        $chart->save();
        Log::info('Charts has been created');
    }

    public function update(ChartsDto $model)
    {
        Log::info('Updating charts', $model->toArray());
        $chart = $this->findById($model->id);
        $chart->name = $model->name;
        $chart->type = $model->type;
        $chart->api_path = $model->apiPath;
        $chart->is_private = $model->isPrivate;
        $chart->owner_id = $model->ownerId;
        $chart->customer_id = $model->customerId;
        $chart->update();
        Log::info('Charts has been updated');
    }

    public function findById($chartId)
    {
        $chart =  Charts::find($chartId);
        if ($chart == null) {
            Log::warning("Not found chart by ID $chartId");
            throw new NotFoundResourceException();
        }
        return $chart;
    }


    public function findAll($user_id)
    {
        $charts =  Charts::where('owner_id', '=', $user_id)->get();
        if ($charts->count() == 0) {
            Log::warning("Not found chart for user by ID $user_id");
            throw new NotFoundResourceException();
        }
        return $charts;
    }

    public function delete($chartId)
    {
        $chart = $this->findById($chartId);
        Log::info('Deleting chart data', (array)  $chart);
        $chart->delete();
        Log::info("Deleted chart by ID $chartId");
    }
}
