<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\VideoMaker;
use App\Services\Interfaces\StonkamServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'device_id' => 'required|numeric|exists:device,device_id',
            'begin_datetime' => 'required|date',
            'end_datetime' => 'required|date',
            'stk_user' => 'required'
        ]);

        $maker = new VideoMaker;

        $maker->stonkamUsername = $validatedData['stk_user'];
        $maker->deviceId =  $validatedData['device_id'];
        $maker->beginDateTime =  Carbon::parse($validatedData['begin_datetime']);
        $maker->endDateTime =  Carbon::parse($validatedData['end_datetime']);

        $result = $this->stonkamService->makeVideo($maker);
        return response($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
