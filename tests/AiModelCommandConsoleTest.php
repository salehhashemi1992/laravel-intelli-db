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

    /** @test */
    public function test_ai_model_command_is_registered()
    {
        $kernel = $this->app->make(Kernel::class);

        $commandList = $kernel->all();

        $this->assertArrayHasKey('ai:model', $commandList);
    }

    /** @test */
    public function test_ai_model_command_options()
    {
        $command = $this->app->make(AiFactoryCommand::class);
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();

        $this->assertArrayHasKey('name', $arguments);
    }

    /** @test */
    public function test_ai_model_command()
    {
        $this->artisan('ai:model', ['name' => 'User'])
            ->assertExitCode(0);

        $this->assertTrue(file_exists(app_path('Models/User.php')));
        $this->assertEquals('Output', file_get_contents(app_path('Models/User.php')));

        // Cleanup
        unlink(app_path('Models/User.php'));
    }
}
