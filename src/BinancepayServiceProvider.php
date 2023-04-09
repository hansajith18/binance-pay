<?php

namespace CryptoPay\Binancepay;

use Illuminate\Support\ServiceProvider;

class BinancepayServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('binancepay.php'),
            ]);
        }
    }

    public function register()
    {
        // Load the package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'binancepay'
        );
    }
}