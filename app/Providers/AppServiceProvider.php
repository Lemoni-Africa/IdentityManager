<?php

namespace App\Providers;

use App\Services\Implementation\VerifyMeService;
use App\Services\Interfaces\IVerifyMeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IVerifyMeService::class, VerifyMeService::class);
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
