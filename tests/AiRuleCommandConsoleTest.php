<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Salehhashemi\LaravelIntelliDb\Console\AiRuleCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class AiRuleCommandConsoleTest extends TestCase
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
        $this->app->singleton('command.ai.rule', function ($app) {
            return new AiRuleCommand($app['files']);
        });

        $this->app->booted(function () {
            $this->app['command.ai.rule']->setLaravel($this->app);
        });
    }

    /** @test */
    public function test_creates_a_rule_without_description()
    {
        $this->artisan('ai:rule', [
            'name' => 'SampleRule',
        ])->assertExitCode(0);

        // Check if the rule file has been created
        $this->assertTrue(File::exists(app_path('Rules/SampleRule.php')));
    }

    /** @test */
    public function test_creates_a_rule_with_description()
    {
        $description = 'validate unique email';

        $this->artisan('ai:rule', [
            'name' => 'SampleRule',
            '--description' => $description,
        ])->assertExitCode(0);

        $this->assertTrue(file_exists(app_path('Rules/SampleRule.php')));
    }
}
