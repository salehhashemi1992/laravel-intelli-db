<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Salehhashemi\LaravelIntelliDb\Console\AiFactoryCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;
use Salehhashemi\LaravelIntelliDb\Tests\TestSupport\Models\User;

class AiFactoryCommandConsoleTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    public function test_ai_factory_command_is_registered()
    {
        $kernel = $this->app->make(Kernel::class);

        $commandList = $kernel->all();

        $this->assertArrayHasKey('ai:factory', $commandList);
    }

    public function test_ai_factory_command_options()
    {
        $command = $this->app->make(AiFactoryCommand::class);
        $definition = $command->getDefinition();
        $options = $definition->getOptions();
        $arguments = $definition->getArguments();

        $this->assertArrayHasKey('name', $arguments);
        $this->assertArrayHasKey('model', $options);
    }

    public function test_ai_factory_command()
    {
        $this
            ->artisan('ai:factory', [
                'name' => 'UserFactory',
                '--model' => User::class,
            ])
            ->assertExitCode(0);

        $this->assertFileExists(database_path('factories/UserFactory.php'));
        $this->assertSame('Output', file_get_contents(database_path('factories/UserFactory.php')));

        // Cleanup
        unlink(database_path('factories/UserFactory.php'));
    }
}
