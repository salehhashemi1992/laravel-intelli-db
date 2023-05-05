<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Foundation\Console\RuleMakeCommand;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

class ExtendedRuleMakeCommand extends RuleMakeCommand
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('ai', 'a', InputOption::VALUE_NONE, 'Use AI to generate rule content');
    }

    public function handle(): bool|null
    {
        $this->handleDomainOption();

        if ($this->option('ai')) {
            // Ask the user for the desired rule content
            $ruleDescription = $this->ask('Please describe the validation rule you want to generate (e.g., "validate unique email")');

            // Create a prompt to generate the content of the rule file
            $prompt = 'Create a validation rule in PHP for '.$this->argument('name').' that does the following: '.$ruleDescription;

            $generatedContent = (new OpenAi())->execute($prompt, 1000);

            // Get generated content and store it
            $this->storeGeneratedContent($generatedContent);

            $this->info('AI-generated content stored at: '.$this->getPath($this->qualifyClass($this->getNameInput())));
        }

        return parent::handle();
    }

    protected function storeGeneratedContent(string $generatedContent)
    {
        // Store the generated content in the correct file and location
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));
        $this->makeDirectory($path);

        // Write the content to the file
        $this->files->put($path, $generatedContent);

        $this->info('Generated content stored at: '.$path);
    }
}
