<?php

namespace App\Services\Interfaces;

interface DeviceServiceInterface
{
    /**
     * the method give device listings,
     * each device item contains device's details.
     * @return array
     */
    public function getAllDevice();
}