<?php

namespace Salehhashemi\LaravelIntelliDb;

use Illuminate\Support\ServiceProvider;
use Salehhashemi\LaravelIntelliDb\Console\AiFactoryCommand;
use Salehhashemi\LaravelIntelliDb\Console\AiMigrationCommand;
use Salehhashemi\LaravelIntelliDb\Console\AiRuleCommand;

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
                AiRuleCommand::class,
                AiMigrationCommand::class,
                AiFactoryCommand::class,
            ]);
        }
    }
}
