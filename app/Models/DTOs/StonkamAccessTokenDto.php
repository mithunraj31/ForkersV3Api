<?php

namespace App\Models\DTOs;

use Carbon\Carbon;

class StonkamAccessTokenDto extends DtoBase
{
    private $accessToken;

    private Carbon $exipreDateTime;

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setExipreDateTime(Carbon $exipreDateTime)
    {
        $this->exipreDateTime = $exipreDateTime;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getExipreDateTime(): Carbon
    {
        return $this->exipreDateTime;
    }
}
