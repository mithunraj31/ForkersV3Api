<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\VideoMaker;

interface StonkamServiceInterface
{
    /**
     * get refresh token if existing in application context,
     * if not request to stonkam server for get a new one.
     * @return long authenticated session id.
     */
    public function refreshAccessToken();

    /**
     *
     */
    public function makeVideo(VideoMaker $maker);

    /**
     * @return Illuminate\Support\Collection;
     */
    public function checkWaitingQueue($id);
}
