<?php

namespace App\Services;

use App\Exceptions\StonkamResultIsFailedException;
use App\Models\DTOs\StonkamAccessTokenDto;
use App\Models\DTOs\VideoMaker;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Facades\Http;
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

        return $this->stonkamAccessToken->getAccessToken();
    }

    public function makeVideo(VideoMaker $maker)
    {
        // check video time range.
        if ($maker->beginDateTime->isAfter($maker->endDateTime)) {
            throw new InvalidArgumentException('DateTime range invalid.');
        }

        // check video's duration time.
        $timeLimit = (int) config('make_video_time_limit');
        $timeDiff = $maker->endDateTime->diffInMinutes($maker->beginDateTime);
        if ($timeDiff > $timeLimit) {
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

        $response = Http::post($endpoint, $data);

        if (!$response->ok()) {
            throw new NotFoundResourceException('Device is Offline.');
        }

        $content =  $response->json();

        // check result is not success
        // https://stackoverflow.com/a/15075609
        if (!filter_var($content['Result'], FILTER_VALIDATE_BOOLEAN)) {
            throw new StonkamResultIsFailedException($content['Reason']);
        }

        return [
            'eventId' => $content['EventId'],
            'videoId' => $content['videoId'],
        ];
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
        $response = Http::post($endpoint, $data);

        if ($response->ok()) {
            $content =  $response->json();
            return intval($content['SessionId']);
        }

        return 0;
    }
}
