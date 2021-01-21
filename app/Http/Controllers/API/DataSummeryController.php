<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataSummery\EventsByOperators;
use App\Services\DataSummeryService;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Utils\CollectionUtility;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DataSummeryController extends Controller
{
    private DataSummeryService $dataSummeryService;

    public function __construct(
        DataSummeryService $dataSummeryService
    ) {
        $this->dataSummeryService = $dataSummeryService;
    }

    public function getEventsByOperators(EventsByOperators $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $operators = explode(',', $request->operator_ids);
        $summery = $this->dataSummeryService->getEventsByOperators($start, $end, $operators);
        return response(['data' => $summery], 200);
    }

}
