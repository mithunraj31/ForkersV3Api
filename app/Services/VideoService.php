<?php

namespace App\Services;

use App\Models\DTOs\VideoDto;
use App\Models\Video;
use App\Models\VideoConverted;
use App\Services\Interfaces\VideoServiceInterface;


class VideoService implements VideoServiceInterface
{
    /**
     * the method saves the video url and cocated video url ,
     * @param validateVideoData
     */

    public function saveVideo(VideoDto $model)
    {
        foreach ($model->videoUrls as $url) {
            $video = new Video;
            $video->deviceId = $model->deviceId;
            $video->eventId = $model->eventId;
            $video->url = $url;
            $video->username = $model->username;
            $video->save();
        }

        $videoConverted = new VideoConverted;
        $videoConverted->id = $model->eventId;
        $videoConverted->url = $model->convertedVideoUrl;

        $videoConverted->save();
    }
}
