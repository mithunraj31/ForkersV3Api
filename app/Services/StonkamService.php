<?php

namespace App\Services;

use App\Models\DTOs\StonkamAccessTokenDto;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Facades\Http;

class StonkamService implements StonkamServiceInterface
{

    private StonkamAccessTokenDto $stonkamAccessToken;


    public function __construct(StonkamAccessTokenDto $stonkamAccessToken)
    {
        $this->stonkamAccessToken = $stonkamAccessToken;
    }


    public function refreshAccessToken()
    {
        if ($this->stonkamAccessToken->getAccessToken() == null
            || $this->stonkamAccessToken->getAccessToken() == 0
            || now()->isAfter($this->stonkamAccessToken->getExipreDateTime())) {
            $accessToken = $this->requestAccessToken();
            $this->stonkamAccessToken->setAccessToken($accessToken);

            $tenMinutesFromNow = now()->addMinutes(10);
            $this->stonkamAccessToken->setExipreDateTime($tenMinutesFromNow);
        }

        return $this->stonkamAccessToken->getAccessToken();
    }

    private function requestAccessToken()
    {
        $endpoint = config('stonkam.hostname').'/RecordDataAuthentication/100';
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
