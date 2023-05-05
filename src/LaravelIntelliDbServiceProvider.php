<?php

namespace Salehhashemi\LaravelIntelliDb;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;
use Salehhashemi\LaravelIntelliDb\Console\ExtendedMigrationMakeCommand;
use Salehhashemi\LaravelIntelliDb\Console\ExtendedRuleMakeCommand;

class LaravelIntelliDbServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ExtendedMigrationMakeCommand::class, function ($app) {
            $filesystem = $app->make(Filesystem::class);
            $composer = $app->make(Composer::class);
            $stubPath = $app->basePath('stubs');

            return new ExtendedMigrationMakeCommand($filesystem, $composer, $stubPath);
        });
    }

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
                ExtendedMigrationMakeCommand::class,
            ]);
        }
    }
}
