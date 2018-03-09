<?php

namespace onethirtyone\backpacksetup;

use Illuminate\Support\ServiceProvider;
use onethirtyone\scifs\App\Classes\Data;
use onethirtyone\scifs\app\Classes\Http;
use onethirtyone\scifs\App\Classes\ScifsService;
use onethirtyone\scifs\App\Console\Commands\CreateAdmin;
use onethirtyone\scifs\App\Console\Commands\DeleteUser;
use onethirtyone\scifs\App\Console\Commands\PortalCounts;
use onethirtyone\scifs\App\Console\Commands\PortalInstall;
use onethirtyone\scifs\App\Providers\ComposerServiceProvider;

class BackpackSetupServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->runningInConsole()) {
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
        //
    }
}
