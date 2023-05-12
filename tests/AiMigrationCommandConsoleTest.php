<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\File;
use Salehhashemi\LaravelIntelliDb\Console\AiMigrationCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class AiMigrationCommandConsoleTest extends BaseTest
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

    /** @test */
    public function test_ai_migration_command()
    {
        $this->artisan('ai:migration', [
            'name' => 'create_users_table',
            '--table' => 'users',
            '--description' => 'Create users table',
        ])->assertExitCode(0);

        $this->assertTrue(File::exists(database_path('migrations')));

        $migrationFile = File::glob(database_path('migrations').'/*_create_users_table.php');
        $this->assertNotEmpty($migrationFile);
        $this->assertStringEqualsFile(reset($migrationFile), 'Output');

        // Cleanup
        File::delete($migrationFile);
    }
}
