<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\VideoDto;
use Illuminate\Http\Request;
use App\Services\Interfaces\VideoServiceInterface;

class VideoController extends Controller
{

    private VideoServiceInterface $videoService;



    public function __construct(VideoServiceInterface $videoService)
    {
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

    }

    public function createEventVideos(Request $request)
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
