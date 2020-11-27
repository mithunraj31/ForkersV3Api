<?php

namespace App\Services;

use App\Models\DTOs\EventDto;
use App\Models\DTOs\EventFilterDto;
use App\Models\DTOs\SensorValueDto;
use App\Models\DTOs\VideoDto;
use App\Models\Event;
use App\Models\VideoConverted;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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

    public function getAllEvent($filter)
    {
        Log::info('Getting all events');
        $queryBuilder = $this->getEventQueryBuilder($filter);

        $results = $queryBuilder->with([ 'device','cameras', 'videos'])->get();

        return $this->mapDaoToDto($results);
    }

    private function getEventQueryBuilder($filter)
    {
        Log::info('Creating query builder');
        $queryBuilder = (new Event())->newQuery();

        if ($filter->deviceId) {
            $queryBuilder->where('device_id', '=', $filter->deviceId);
        }

        if ($filter->startDatetime != null
            && $filter->endDateTime != null) {
            // check video time range.
            if ($filter->startDatetime->isAfter($filter->endDateTime)) {
                Log::warning('Time range is invalid  ');
                throw new InvalidArgumentException('DateTime range invalid.');
            }

            $queryBuilder->whereBetween('time', [
                $filter->getStartDateTimeUtc()->format('Y-m-d H:i:s'),
                $filter->getEndDateTimeUtc()->format('Y-m-d H:i:s'),
            ]);
        }

        if ($filter->stkUser) {
            $queryBuilder->where('username', '=', $filter->stkUser);
        }

        $perPage = 15;
        if ($filter->perPage) {
            $perPage = $filter->perPage;
        }

        $pageNumber = 0;
        if ($filter->page) {
            $pageNumber = $filter->page;
        }

        $queryBuilder->paginate($perPage, ['*'], 'page', $pageNumber);

        return $queryBuilder->orderBy('time', $filter->orderBy);
    }

    private function mapDaoToDto($results) {
        $events = $results->map(function ($event) {
            $model = new EventDto;

            $model->id = $event['id'];

            $model->deviceId = $event['event_id'];

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
                return $v['url'];
            });

            $convertedVideo = VideoConverted::where('id', '=' , $event['event_id'])->first();

            if ($convertedVideo) {
                $video->convertedVideoUrl = $convertedVideo['url'];
            }

            $model->video = $video;

            $model->numberOfCameras = collect($event['cameras'])->count();

            return $model;
        });
        return $events;
    }
}
