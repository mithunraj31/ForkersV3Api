<?php

namespace App\Services;

use App\Models\DTOs\VideoDto;
use App\Models\Video;
use App\Models\VideoConverted;
use App\Services\Interfaces\VideoServiceInterface;
use Illuminate\Database\QueryException;
use InvalidArgumentException;

class VideoService implements VideoServiceInterface
{
    /**
     * the method saves the video url and cocated video url ,
     * @param validateVideoData
     */

    public function saveVideo(VideoDto $model)
    {
        try {
            $videoConverted = new VideoConverted;
            $videoConverted->id = $model->eventId;
            $videoConverted->url = $model->convertedVideoUrl;

            $videoConverted->save();
        } catch(QueryException $exception) {
            $message = 'Could not save converted video.';
            if ($exception->getCode() == '23000') {
                $eventId = $model->eventId;
                $message = "Converted video with event ID:$eventId is already exists.";
            }

            throw new InvalidArgumentException($message);
        }

        $videos = [];

        foreach ($model->videoUrls as $url) {
            $videos[] = [
                'device_id' => $model->deviceId,
                'event_id' => $model->eventId,
                'url' => $url,
                'username' => $model->username,
            ];
        }

        Video::insert($videos);
    }
}
