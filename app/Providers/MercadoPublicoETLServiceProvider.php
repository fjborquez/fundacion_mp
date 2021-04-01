<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Etl\MercadoPublicoETL;

class MercadoPublicoETLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MercadoPublicoETL::class, function ($app) {
            return new MercadoPublicoETL();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
