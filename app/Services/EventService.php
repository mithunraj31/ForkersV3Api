<?php

namespace App\Services;

use App\Models\DTOs\EventDto;
use App\Models\DTOs\SensorValueDto;
use App\Models\DTOs\VideoDto;
use App\Models\Event;
use App\Models\VideoConverted;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class EventService implements EventServiceInterface
{
    /**
     * the method give summary of device event,
     * count by accelerate, decelerate, eventImpact, turnLeft, turnRight and button events.
     * @param string $stkUser (optional) count only event has an username equals the value.
     * @return mixed number of each event.
     */
    public function getEventSummary($stkUser = null)
    {
        Log::info('Getting Event summary of user');
        return Event::getEventSummary($stkUser);
    }



    public function findById($eventId)
    {
        $event =  Event::where('event_id', '=', $eventId)->get();
        if ($event == null) {
            throw new NotFoundResourceException();
        }
        return $event;
    }

    public function findVideoById($eventId)
    {
        $video =  VideoConverted::where('id', '=', $eventId)->first();
        if ($video == null) {
            throw new NotFoundResourceException();
        }
        return config('app.s3') . '/' . $video['url'];
    }

    public function getAllEvent($filter)
    {
        Log::info('Getting all events');
        $queryBuilder = $this->getEventQueryBuilder($filter);


        $results = $queryBuilder->with(['device', 'cameras', 'videos'])->get();

        return $this->mapDaoToDto($results);
    }

    public function count($filter)
    {
        $queryBuilder = $this->getEventQueryBuilder($filter);
        $paginator = $queryBuilder->paginate();
        return $paginator->total();
    }

    private function getEventQueryBuilder($filter)
    {
        Log::info('Creating query builder');
        $queryBuilder = (new Event())->newQuery();

        if ($filter == null) {
            return $queryBuilder;
        }

        if ($filter->deviceId) {
            $queryBuilder->where('device_id', '=', $filter->deviceId);
        }

        if (
            $filter->startDatetime != null
            && $filter->endDateTime != null
        ) {
            // check video time range.
            if ($filter->startDatetime->isAfter($filter->endDateTime)) {
                Log::warning('Time range is invalid  ');
                throw new InvalidArgumentException('DateTime range invalid.');
            }

            $queryBuilder->whereBetween('time', [
                $filter->getStartDateTimeUtc()->format('Y-m-d H:i:s'),
                $filter->getEndDateTimeUtc()->format('Y-m-d H:i:s'),
            ]);
        } else {
            $perPage = 15;
            if ($filter->perPage) {
                $perPage = (int) $filter->perPage;
            }

            $pageNumber = 0;
            if ($filter->page) {
                $pageNumber = (int) $filter->page;
            }

            $queryBuilder->paginate($perPage, ['*'], 'page', $pageNumber);
        }

        if ($filter->stkUser) {
            $queryBuilder->where('username', '=', $filter->stkUser);
        }

        return $queryBuilder->orderBy('time', $filter->orderBy);
    }

    private function mapDaoToDto($results)
    {
        $events = $results->map(function ($event) {
            $model = new EventDto;

            $model->id = $event['id'];

            $model->eventId = $event['event_id'];

            $model->deviceId = $event['device_id'];

            $model->driverId = $event['driver_id'];

            $model->type = $event['type'];

            $model->videoId = $event['video_id'];

            $model->time = $event['time'];

            $model->username = $event['username'];

            $sensorValue = new SensorValueDto;

            $sensorValue->latitude = $event['latitude'];

            $sensorValue->longitude = $event['longitude'];

            $sensorValue->gx = $event['gx'];

            $sensorValue->gy = $event['gy'];

            $sensorValue->gz = $event['gz'];

            $sensorValue->roll = $event['roll'];

            $sensorValue->pitch = $event['pitch'];

            $sensorValue->yaw = $event['yaw'];

            $sensorValue->status = $event['status'];

            $sensorValue->direction = $event['direction'];

            $sensorValue->speed = $event['speed'];

            $model->sensorValue = $sensorValue;

            $video = new VideoDto;

            $video->videoUrls = collect($event['videos'])->map(function ($v) {
                return config('app.s3') . '/' . $v['url'];
            });

            $convertedVideo = VideoConverted::where('id', '=', $event['event_id'])->first();

            if ($convertedVideo) {
                $video->convertedVideoUrl = config('app.s3') . '/' . $convertedVideo['url'];
            }

            $model->video = $video;

            $model->numberOfCameras = collect($event['cameras'])->count();

            return $model;
        });
        return $events;
    }
}
