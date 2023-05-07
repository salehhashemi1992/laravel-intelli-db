<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Orchestra\Testbench\TestCase;
use Salehhashemi\LaravelIntelliDb\Console\AiMigrationCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class AiMigrationCommandConsoleTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    /** @test */
    public function test_ai_migration_command_is_registered()
    {
        $kernel = $this->app->make(Kernel::class);

        $commandList = $kernel->all();

        $this->assertArrayHasKey('ai:migration', $commandList);
    }

    /** @test */
    public function test_ai_migration_command_options()
    {
        $command = $this->app->make(AiMigrationCommand::class);
        $definition = $command->getDefinition();
        $options = $definition->getOptions();
        $arguments = $definition->getArguments();

        $this->assertArrayHasKey('name', $arguments);

        $this->assertArrayHasKey('description', $options);
        $this->assertArrayHasKey('table', $options);
        $this->assertArrayHasKey('path', $options);
    }
}
