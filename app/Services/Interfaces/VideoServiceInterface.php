<?php

namespace App\Services\Interfaces;

interface VideoServiceInterface
{
    /**
     * the method saves the video url and concated video url ,
     * @param video[]
     * @return status
     */
    public function saveVideo($validateVideoData);

    public function updateVideo($validateVideoData);

    public function getAllVideos($request);
}
