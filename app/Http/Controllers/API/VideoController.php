<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoConverted;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { {
            $page = $request->query('page') ? (int)$request->query('page') : 1;
            $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
            $videos = $this->videoService->getAllVideos($request);

            $pageItems = $videos->forPage($page, $perPage);
            return [
                'data' => $pageItems,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($videos)
                ]
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateVideoData = $request->validate([
            'event_id' => 'required',
            'url' => 'required',
            'username' => 'required|exists:App\Models\User,stk_user',
            'device_id' => 'required|exists:App\Models\Device,device_id',
        ]);

        //$data = ['data' => $requests->all()];
        // $validateConcatedVideo = Validator::make($data, [
        //     'data.event_id' => 'required',
        //     'data.url' => 'required',
        //     'data.username' => 'required|exists:App\Models\User,stk_user',
        //     'data.device_id' => 'required|exists:App\Models\Device,device_id',
        //     'data.video.url' => 'required|string',
        // ]);
        // $validateVideoConvertedData = $request->validate([
        //     'converted_url' => 'required',
        // ]);
        $video = new Video($validateVideoData);
        // $video_converted = new VideoConverted($validateVideoConvertedData);

        // $this->videoService->saveVideo($video);


        return response(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        return $video;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $validateVideoData = $request->validate([
            'event_id' => 'required',
            'url' => 'required',
            'username' => 'required|exists:App\Models\User,stk_user',
            'device_id' => 'required|exists:App\Models\Device,device_id'

        ]);
        $video->update($request->all());

        // $this->videoService->updateVideo($validateVideoData);

        return response($video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return response(['message' => 'Success!'], 200);
    }
}
