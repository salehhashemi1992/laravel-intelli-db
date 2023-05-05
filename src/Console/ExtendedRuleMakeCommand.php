<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): bool|null
    {
        try {
            if ($this->option('ai')) {
                // Ask the user for the desired rule content
                $ruleDescription = $this->ask('Please describe the validation rule you want to generate (e.g., "validate unique email")');

                $prompt = $this->createPrompt($ruleDescription);

                $generatedContent = (new OpenAi())->execute($prompt, 1000);

                // Get generated content and store it
                $this->storeGeneratedContent($generatedContent);

                $this->info('AI-generated content stored at: '.$this->getPath($this->qualifyClass($this->getNameInput())));

                // Return true when AI option is used and content is stored successfully
                return true;
            } else {
                return parent::handle();
            }
        } catch (RequestException $e) {
            $this->error('Error fetching AI-generated content: '.$e->getMessage());

            return false;
        } catch (FileNotFoundException $e) {
            $this->error('Error storing AI-generated content: '.$e->getMessage());

            return false;
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return false;
        }
    }

    /**
     * Store the generated content in the correct file and location
     */
    protected function storeGeneratedContent(string $generatedContent): void
    {
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));
        $this->makeDirectory($path);

        $this->files->put($path, $generatedContent);

        $this->info('Generated content stored at: '.$path);
    }

    /**
     * Create a prompt to generate the content of the rule file
     */
    private function createPrompt(string $ruleDescription): string
    {
        return "Generate the PHP code for a Laravel validation rule class named '".$this->argument('name')."' that implements the Rule interface and does the following:".
            "\n$ruleDescription".
            "\nProvide only the final Laravel validation rule class code without any explanations or additional context.";
    }
}
