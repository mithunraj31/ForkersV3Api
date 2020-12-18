<?php

namespace App\Providers;

use App\Models\DTOs\StonkamAccessTokenDto;
use App\Services\DeviceService;
use App\Services\CameraService;
use App\Services\CustomerService;
use App\Services\DriverService;
use App\Services\EventService;
use App\Services\GroupService;
use App\Services\Interfaces\CameraServiceInterface;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Services\Interfaces\DeviceServiceInterface;
use App\Services\Interfaces\DriverServiceInterface;
use App\Services\Interfaces\EventServiceInterface;
use App\Services\Interfaces\GroupServiceInterface;
use App\Services\Interfaces\OperatorServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\OperatorService;
use App\Services\Interfaces\VideoServiceInterface;
use App\Services\RoleService;
use App\Services\StonkamService;
use App\Services\UserService;
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
        DeviceServiceInterface::class => DeviceService::class,
        EventServiceInterface::class => EventService::class,
        OperatorServiceInterface::class => OperatorService::class,
        CameraServiceInterface::class => CameraService::class,
        VideoServiceInterface::class => VideoService::class,
        CustomerServiceInterface::class => CustomerService::class,
        DriverServiceInterface::class => DriverService::class,
        UserServiceInterface::class => UserService::class,
        RoleServiceInterface::class => RoleService::class,
        GroupServiceInterface::class => GroupService::class

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
