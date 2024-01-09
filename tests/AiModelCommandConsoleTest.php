<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Salehhashemi\LaravelIntelliDb\Console\AiFactoryCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class AiModelCommandConsoleTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    public function test_ai_model_command_is_registered()
    {
        $kernel = $this->app->make(Kernel::class);

        $commandList = $kernel->all();

        $this->assertArrayHasKey('ai:model', $commandList);
    }

    public function test_ai_model_command_options()
    {
        $command = $this->app->make(AiFactoryCommand::class);
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();

        $this->assertArrayHasKey('name', $arguments);
    }

    public function test_ai_model_command()
    {
        $this->artisan('ai:model', ['name' => 'User'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Models/User.php'));
        $this->assertSame('Output', file_get_contents(app_path('Models/User.php')));

        // Cleanup
        unlink(app_path('Models/User.php'));
    }
}
