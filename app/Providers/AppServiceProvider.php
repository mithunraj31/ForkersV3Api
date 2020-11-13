<?php

namespace App\Providers;

use App\Models\DTOs\StonkamAccessTokenDto;
use App\Services\CameraService;
use App\Services\EventService;
use App\Services\Interfaces\CameraServiceInterface;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\EventServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Services\Interfaces\VideoServiceInterface;
use App\Services\StonkamDeviceService;
use App\Services\StonkamService;
use App\Services\VideoService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        StonkamAccessTokenDto::class => StonkamAccessTokenDto::class,


        //services
        StonkamServiceInterface::class => StonkamService::class,
        DeviceServiceInterface::class => StonkamDeviceService::class,
        EventServiceInterface::class => EventService::class,
        CameraServiceInterface::class => CameraService::class,
        VideoServiceInterface::class => VideoService::class
    ];


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
