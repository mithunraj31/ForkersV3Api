<?php

namespace App\Services;

use App\Exceptions\StonkamResultIsFailedException;
use App\Models\DTOs\StonkamAccessTokenDto;
use App\Models\DTOs\VideoMaker;
use App\Services\Interfaces\StonkamServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class StonkamService implements StonkamServiceInterface
{

    private StonkamAccessTokenDto $stonkamAccessToken;

    public function __construct(StonkamAccessTokenDto $stonkamAccessToken)
    {
        $this->stonkamAccessToken = $stonkamAccessToken;
    }


    public function refreshAccessToken()
    {
        Log::info('stonkam api requested for refreshing access token ');
        if (
            $this->stonkamAccessToken->getAccessToken() == null
            || $this->stonkamAccessToken->getAccessToken() == 0
            || now()->isAfter($this->stonkamAccessToken->getExipreDateTime())
        ) {
            $accessToken = $this->requestAccessToken();
            $this->stonkamAccessToken->setAccessToken($accessToken);

            $tenMinutesFromNow = now()->addMinutes(10);
            $this->stonkamAccessToken->setExipreDateTime($tenMinutesFromNow);
        }
        Log::info('Refreshing access token is successful');
        return $this->stonkamAccessToken->getAccessToken();
    }

    public function makeVideo(VideoMaker $maker)
    {
        Log::info('making video for the requested time range', $maker->beginDateTime, '-', $maker->endDateTime);
        // check video time range.
        if ($maker->beginDateTime->isAfter($maker->endDateTime)) {
            Log::warning("Time range is invalid", $maker->beginDateTime, '-', $maker->endDateTime);
            throw new InvalidArgumentException('DateTime range invalid.');
        }

        // check video's duration time.
        $timeLimit = (int) config('make_video_time_limit');
        $timeDiff = $maker->endDateTime->diffInMinutes($maker->beginDateTime);
        if ($timeDiff > $timeLimit) {
            Log::warning("Video's duration time is more than $timeLimit minutes.");
            throw new InvalidArgumentException("Video's duration time is more than $timeLimit minutes.");
        }

        $username = $maker->stonkamUsername;
        $sessionId = $this->refreshAccessToken();

        if (!isset($username)) {
            $username = config('stonkam.auth.admin.username');
        }

        $endpoint = config('stonkam.hostname') . "/SetUploadVideoTime/100?UserName=$username&SessionId=$sessionId";

        $data = [
            'BeginTime' => $maker->getBeginDateTimeUtc()->format('Y-m-d H:i:s'),
            'EndTime' => $maker->getEndDateTimeUtc()->format('Y-m-d H:i:s'),
            'DeviceId' => $maker->deviceId,
        ];
        Log::info('stonkam api called to upload the video for device id', $maker->deviceId, 'with user name', $username);
        $response = Http::post($endpoint, $data);

        if (!$response->ok()) {
            Log::warning('Device is offline');
            throw new NotFoundResourceException('Device is Offline.');
        }

        $content =  $response->json();

        // check result is not success
        // https://stackoverflow.com/a/15075609
        if (!filter_var($content['Result'], FILTER_VALIDATE_BOOLEAN)) {
            Log::warning('Device is online but cannot upload the video the reason is ', $content['Reason']);
            throw new StonkamResultIsFailedException($content['Reason']);
        }

        return [
            'eventId' => $content['EventId'],
            'videoId' => $content['videoId'],
        ];

        Log::info('Video making is successful');
    }

    private function requestAccessToken()
    {
        $endpoint = config('stonkam.hostname') . '/RecordDataAuthentication/100';
        $data = [
            'UserName' => config('stonkam.auth.admin.username'),
            'Password' => config('stonkam.auth.admin.password'),
            'Version' => config('stonkam.auth.admin.version'),
            'AuthType' => intval(config('stonkam.auth.admin.authtype')),
        ];
        Log::info('stonkam api requested for access token ');
        $response = Http::post($endpoint, $data);

        if ($response->ok()) {
            Log::info('Accesss token is received');
            $content =  $response->json();
            return intval($content['SessionId']);
        }
        Log::warning('Somethinng went wrong in receiving the access token');
        return 0;
    }
}
