<?php

namespace App\Providers;

use App\Services\MercadoPublico\MercadoPublicoETL;
use Illuminate\Support\ServiceProvider;

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
}
