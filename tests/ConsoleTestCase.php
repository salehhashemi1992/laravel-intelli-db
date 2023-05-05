<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class ConsoleTestCase extends BaseTestCase
{
    protected function runCommand(string $command, array $arguments = [], array $interactiveInput = []): CommandTester
    {
        $this->withoutMockingConsoleOutput();

        $command = new $command(new Filesystem());
        $command->setLaravel($this->app);

        $command->setLaravel($this->app);

        $tester = new CommandTester($command);
        $tester->setInputs($interactiveInput);

        $tester->execute($arguments);

        return $tester;
    }
}
