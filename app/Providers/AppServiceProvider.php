<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MyService;
use App\Services\Login\JWTService;
use App\Model\Base\Model;

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

        $db_host=env('DB_HOST', '');
        $db_user=env('DB_USERNAME', '');
        $db_pass=env('DB_PASSWORD', '');
        $db_name=env('DB_DATABASE', '');
        $db_charset='utf-8';
        $db_port=env('DB_PORT', '');
        $db_main=new Model();
        $db_main->connect($db_host,$db_user,$db_pass,$db_name,$db_charset,$db_port);
        $this->app->instance(Model::class, $db_main);
    }
}
