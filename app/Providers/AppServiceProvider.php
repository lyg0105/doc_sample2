<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MyService;
use App\Services\Login\JWTService;

class AppServiceProvider extends ServiceProvider
{
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
        $myService = new MyService();
        $this->app->instance(MyService::class, $myService);

        $jWTService = new JWTService();
        $this->app->instance(JWTService::class, $jWTService);
    }
}
