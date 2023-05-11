<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;
use Salehhashemi\LaravelIntelliDb\OpenAi;

abstract class BaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(OpenAi::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')->zeroOrMoreTimes()->andReturn('Output');
        });

        Schema::shouldReceive('hasTable')->zeroOrMoreTimes()->andReturn(true);
        Schema::shouldReceive('getColumnListing')->zeroOrMoreTimes()->andReturn(['id', 'name', 'email']);
    }
}
