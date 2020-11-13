<?php

namespace App\Services;

use App\Http\Resources\VideoResources;
use App\Models\Video;
use App\Models\VideoConverted;
use App\QueryBuilders\VideoQueryBuilder;
use App\Services\Interfaces\VideoServiceInterface;


class VideoService implements VideoServiceInterface
{
    /**
     * the method saves the video url and cocated video url ,
     * @param video[]
     * @return status
     */

    public function saveVideo($validateVideoData)
    {
        $videos = [];
        if ($validateVideoData) {
            foreach ($validateVideoData->url as $videoUrl) {
                $videos[] = [
                    'event_id' => $validateVideoData->event_id,
                    'url' =>  $videoUrl,
                    'username' => $validateVideoData->username,
                    'device_id' => $validateVideoData->device_id,
                ];
            }
        }


        Video::insert($videos);
        // $videoConverted = new VideoConverted($validateConcatedVideo->all());
        // $videoConverted->save();
    }

    /**
     * the method saves the video url and cocated video url ,
     * @param video[]
     * @return status
     */

    public function updateVideo($validateVideoData)
    {
        $videos = [];
        if ($validateVideoData) {
            foreach ($validateVideoData->url as $videoUrl) {
                $videos[] = [
                    'event_id' => $validateVideoData->event_id,
                    'url' =>  $videoUrl,
                    'username' => $validateVideoData->username,
                    'device_id' => $validateVideoData->device_id,
                ];
            }
        }


        Video::update($videos);
        // $videoConverted = new VideoConverted($validateConcatedVideo->all());
        // $videoConverted->save();
    }

    public function getAllVideos($request)
    {
        // $builder = Video::with(['url']);
        $pager = VideoQueryBuilder::applyWithPaginator($request);
        return new VideoResources($pager);
    }
}
