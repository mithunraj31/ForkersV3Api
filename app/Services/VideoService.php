<?php

namespace App\Services;

use App\Models\DTOs\VideoDto;
use App\Models\Video;
use App\Models\VideoConverted;
use App\Services\Interfaces\VideoServiceInterface;
use Illuminate\Support\Facades\Log;

class VideoService implements VideoServiceInterface
{
    /**
     * the method saves the video url and cocated video url ,
     * @param validateVideoData
     */

    public function saveVideo(VideoDto $model)
    {
        Log::info('Saving video', $model->toArray());
        foreach ($model->videoUrls as $url) {
            $video = new Video;
            $video->deviceId = $model->deviceId;
            $video->eventId = $model->eventId;
            $video->url = $url;
            $video->username = $model->username;
            $video->save();
        }
        Log::info('Video saving is successful');

        Log::info('Saving converted video url');

        $videoConverted = new VideoConverted;
        $videoConverted->id = $model->eventId;
        $videoConverted->url = $model->convertedVideoUrl;

        $videoConverted->save();

        Log::info('Converted video is saved successfully');
    }
}
