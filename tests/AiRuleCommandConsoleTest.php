<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Orchestra\Testbench\TestCase;
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
