<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\VideoDto;

interface VideoServiceInterface
{
    /**
     * the method saves the video url and concated video url ,
     * @param VideoDto
     */
    public function saveVideo(VideoDto $model);
}
