<?php

namespace App\Providers;

use App\Models\DTOs\StonkamAccessTokenDto;
use App\Services\DeviceService;
use App\Services\EventService;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\EventServiceInterface;
use App\Services\Interfaces\OperatorServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Services\OperatorService;
use App\Services\StonkamDeviceService;
use App\Services\StonkamService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        StonkamAccessTokenDto::class => StonkamAccessTokenDto::class,


        //services
        StonkamServiceInterface::class => StonkamService::class,
        DeviceServiceInterface::class => DeviceService::class,
        EventServiceInterface::class => EventService::class,
        OperatorServiceInterface::class => OperatorService::class
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
