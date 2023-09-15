<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Salehhashemi\LaravelIntelliDb\Console\AiFactoryCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

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
        // Create a model for the purpose of the test
        $modelPath = app_path('Models/User.php');
        file_put_contents($modelPath, "<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; class User extends Model { }");

        require_once $modelPath;

        $this->artisan('ai:factory', [
            'name' => 'UserFactory',
            '--model' => 'User',
        ])
            ->assertExitCode(0);

        $this->assertTrue(file_exists(database_path('factories/UserFactory.php')));
        $this->assertEquals('Output', file_get_contents(database_path('factories/UserFactory.php')));

        // Cleanup
        unlink(database_path('factories/UserFactory.php'));
        unlink($modelPath);  // Delete the test model
    }
}
