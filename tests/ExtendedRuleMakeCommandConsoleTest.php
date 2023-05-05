<?php

use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;
use Salehhashemi\LaravelIntelliDb\Tests\ConsoleTestCase;

class ExtendedRuleMakeCommandConsoleTest extends ConsoleTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    /** @test */
    public function test_example()
    {
        $this->assertTrue(true);
    }
}
