<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\OperatorServiceInterface;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    private OperatorServiceInterface $operatorService;

    public function __construct(OperatorServiceInterface $operatorService)
    {
        $this->operatorService = $operatorService;
    }
    public function getDriveSummery(Request $request, $opertorId)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        if ($opertorId && $start && $end) {
            $driveSummery = $this->operatorService->getDriveSummery($opertorId, $start, $end);
            return response($driveSummery,200);
        } else {
            return response(["message" => "Invalid request"], 400);
        }
    }
    public function getOperatorEvents(Request $request, $opertorId)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        if ($opertorId && $start && $end) {
            $events = $this->operatorService->getOperatorEvents($opertorId, $start, $end);
            return ["data" => $events];
        } else {
            return response(["message" => "Invalid request"], 400);
        }
    }
}
