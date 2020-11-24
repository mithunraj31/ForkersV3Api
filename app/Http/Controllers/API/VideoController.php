<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\VideoDto;
use App\Models\DTOs\VideoMaker;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Http\Request;
use App\Services\Interfaces\VideoServiceInterface;
use Carbon\Carbon;

class VideoController extends Controller
{

    private VideoServiceInterface $videoService;

    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService, VideoServiceInterface $videoService)
    {
        $this->stonkamService = $stonkamService;
        $this->videoService = $videoService;
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
        return response()->json([
            'data' => $result
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addEventVideos(Request $request)
    {
        $validateVideoData = $request->validate([
            'event_id' => 'required',
            'urls' => 'required',
            'username' => 'required|exists:App\Models\User,stk_user',
            'device_id' => 'required|exists:App\Models\Device,device_id',
            'converted_url' => 'required',
        ]);
        $video = new VideoDto();
        $video->deviceId = $request->device_id;
        $video->eventId = $request->event_id;
        $video->videoUrls = $request->urls;
        $video->username = $request->username;
        $video->convertedVideoUrl = $request->converted_url;



        $this->videoService->saveVideo($video);

        return response([], 201);
    }
}
