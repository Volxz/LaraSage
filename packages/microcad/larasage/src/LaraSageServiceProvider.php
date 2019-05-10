<?php

namespace Microcad\LaraSage;

use Illuminate\Support\ServiceProvider;

class LaraSageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/larasage.php' => config_path('larasage.php'),
        ]);
    }
}
