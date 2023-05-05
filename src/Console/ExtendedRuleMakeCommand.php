<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Exception;
use Illuminate\Foundation\Console\RuleMakeCommand;
use Illuminate\Http\Client\RequestException;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

class ExtendedRuleMakeCommand extends RuleMakeCommand
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('ai', 'a', InputOption::VALUE_NONE, 'Use AI to generate rule content');
        $this->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'The description of the validation rule');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildClass($name): string
    {
        if ($this->option('ai')) {
            $ruleDescription = $this->option('description');

            if (! $ruleDescription) {
                // Ask the user for the desired rule content
                $ruleDescription = $this->ask('Please describe the validation rule you want to generate (e.g., "validate unique email")');
            }

            $prompt = $this->createPrompt($ruleDescription);

            try {
                return (new OpenAi())->execute($prompt, 1000);
            } catch (RequestException $e) {
                $this->error('Error fetching AI-generated content: '.$e->getMessage());
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        return parent::buildClass($name);
    }

    /**
     * Create a prompt to generate the content of the rule file
     */
    private function createPrompt(string $ruleDescription): string
    {
        return "Generate the PHP code for a Laravel validation rule class named '".$this->argument('name')."' that implements the Rule interface and does the following:".
            "\n$ruleDescription".
            "\nProvide only the final Laravel validation rule class code (include everything like php tag and namespace) without any explanations or additional context.";
    }
}
