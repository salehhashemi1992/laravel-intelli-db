<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Salehhashemi\LaravelIntelliDb\Console\ExtendedRuleMakeCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class ExtendedRuleMakeCommandConsoleTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Register the custom command
        $this->app->singleton('command.rule.make', function ($app) {
            return new ExtendedRuleMakeCommand($app['files']);
        });
        $this->app->booted(function () {
            $this->app['command.rule.make']->setLaravel($this->app);
        });
    }

    /** @test */
    public function test_creates_a_rule_without_ai_option()
    {
        $this->artisan('make:rule', [
            'name' => 'SampleRule',
        ])->assertExitCode(0);

        // Check if the rule file has been created
        $this->assertTrue(File::exists(app_path('Rules/SampleRule.php')));
    }

    /** @test */
    public function test_creates_a_rule_with_ai_option_and_description()
    {
        $description = 'validate unique email';

        $this->artisan('make:rule', [
            'name' => 'SampleRule',
            '--ai' => true,
            '--description' => $description,
        ])->assertExitCode(0);

        $this->assertTrue(file_exists(app_path('Rules/SampleRule.php')));
    }
}
