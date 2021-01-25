<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\ChartsDto;
use App\Services\Interfaces\ChartServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartsController extends Controller
{
    private ChartServiceInterface $chartService;

    public function __construct(ChartServiceInterface $chartService)
    {
        $this->chartService = $chartService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userId = Auth::user()->id;
        $charts = $this->chartService->findAll($userId);
        return response($charts, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateChartData = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'api_path' => 'required',
            'is_private' => 'required',
            'customer_id' => 'required|exists:App\Models\Customer,id',
        ]);
        $chart = new ChartsDto();
        $chart->name = $validateChartData['name'];
        $chart->type = $validateChartData['type'];
        $chart->apiPath = $validateChartData['api_path'];
        $chart->isPrivate = $validateChartData['is_private'];
        $chart->ownerId = Auth::user()->id;
        $chart->customerId = $validateChartData['customer_id'];
        $this->chartService->create($chart);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($chartId)
    {
        $chart = $this->chartService->findById($chartId);
        return response([
            'data' => $chart
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $chartId)
    {

        $validateChartData = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'api_path' => 'required',
            'is_private' => 'required',
            'customer_id' => 'required|exists:App\Models\Customer,id',
        ]);
        $chart = new ChartsDto();
        $chart->id = $chartId;
        $chart->name = $validateChartData['name'];
        $chart->type = $validateChartData['type'];
        $chart->apiPath = $validateChartData['api_path'];
        $chart->isPrivate = $validateChartData['is_private'];
        $chart->ownerId = Auth::user()->id;
        $chart->customerId = $validateChartData['customer_id'];
        $this->chartService->update($chart);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($chartId)
    {
        $this->chartService->delete($chartId);
        return response(['message' => 'Success!'], 200);
    }
}
