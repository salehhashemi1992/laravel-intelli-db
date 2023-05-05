<?php

namespace Salehhashemi\LaravelIntelliDb;

use Illuminate\Support\ServiceProvider;
use Salehhashemi\LaravelIntelliDb\Console\ExtendedRuleMakeCommand;

class LaravelIntelliDbServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/intelli-db.php' => config_path('intelli-db.php'),
            ], 'config');

            $this->commands([
                ExtendedRuleMakeCommand::class,
            ]);
        }
    }
}
