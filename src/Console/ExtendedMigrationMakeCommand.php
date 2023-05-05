<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;

class ExtendedMigrationMakeCommand extends MigrateMakeCommand
{
    public function __construct(Filesystem $files, Composer $composer, $stubPath)
    {
        parent::__construct(new ExtendedMigrationCreator($files, $stubPath), $composer);
    }

    protected function configure()
    {
        parent::configure();

        $this->addOption('ai', 'a', InputOption::VALUE_NONE, 'Use AI to generate migration content');
        $this->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'The description of the migration');
    }

    public function handle()
    {
        $useAI = $this->option('ai');
        $description = $this->option('description') ?: '';

        if ($useAI) {
            if (empty($description)) {
                $description = $this->ask('Please describe the migration you want to generate (e.g., "create users table with name, email, and password columns")');
            }
            $this->creator->setAiOptions($useAI, $description);
        }

        parent::handle();
    }
}
