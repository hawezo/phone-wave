<?php

namespace FutureGadgetLab\PhoneWave;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use FutureGadgetLab\PhoneWave\DMailMiddleware;

class PhoneWaveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('phone-wave.php'),
            ], 'config');
        }
        
        $this->registerMiddleware();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'phone-wave');
    }

    protected function registerMiddleware()
    {
        $this->app[Kernel::class]->pushMiddleware(DMailMiddleware::class);
    }
}
