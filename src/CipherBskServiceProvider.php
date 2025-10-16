<?php

namespace CipherBsk;

/**
 * Laravel Service Provider for CipherBsk
 * 
 * Note: This file will only be loaded when the package is installed
 * in a Laravel application. The Illuminate classes will be available
 * in that context.
 * 
 * @package CipherBsk
 */

use Illuminate\Support\ServiceProvider;

class CipherBskServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cipher-bsk.php', 'cipher-bsk'
        );

        $this->app->singleton(CipherBsk::class, function () {
            return new CipherBsk();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cipher-bsk.php' => config_path('cipher-bsk.php'),
        ], 'config');
    }
}
