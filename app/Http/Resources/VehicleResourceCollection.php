<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VehicleResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
