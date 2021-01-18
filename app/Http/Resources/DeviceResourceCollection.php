<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DeviceResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
