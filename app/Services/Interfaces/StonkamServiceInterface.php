<?php

namespace App\Services\Interfaces;

interface StonkamServiceInterface
{
    /**
     * get refresh token if existing in application context,
     * if not request to stonkam server for get a new one.
     * @return long authenticated session id.
     */
    public function refreshAccessToken();
}
