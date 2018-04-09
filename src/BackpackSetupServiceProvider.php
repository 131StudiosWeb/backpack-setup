<?php

namespace onethirtyone\backpacksetup;

use Illuminate\Support\ServiceProvider;
use onethirtyone\backpacksetup\App\Console\Commands\BackpackSetup;


class BackpackSetupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/setup.php' => config_path('onethirtyone/backpacksetup.php'),
        ],'config');


        if ($this->app->runningInConsole()) {
            $this->commands([
                BackpackSetup::class
            ]);
        }
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/setup.php', 'onethirtyone/backpacksetup'
        );
    }
}
