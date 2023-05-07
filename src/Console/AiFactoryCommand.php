<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

class AiFactoryCommand extends Command
{
    protected $name = 'ai:factory';

    protected $description = 'Create a new factory using AI';

    protected function configure()
    {
        $this->addArgument('name', InputOption::VALUE_REQUIRED, 'The name of the factory')
            ->addOption('model', 'm', InputOption::VALUE_REQUIRED, 'The model for the factory');
    }

    public function handle(): int
    {
        $name = $this->argument('name');
        if (! $name) {
            $name = $this->ask($this->promptForMissingArgumentsUsing()['name']);
        }

        $model = $this->option('model');

        if (! $model) {
            $model = $this->ask('Please provide the model for the factory');
        }

        if (! class_exists($model) || ! is_subclass_of($model, Model::class)) {
            $this->error("The provided model '{$model}' does not exist or is not a valid Eloquent model.");

            return 1;
        }

        $table = (new $model)->getTable();

        $schema = Schema::getColumnListing($table);

        $prompt = $this->createAiPrompt($name, $model, $schema);

        try {
            $factoryContent = $this->fetchAiGeneratedContent($prompt);
            $this->createFactoryFile($model, $factoryContent);
        } catch (RequestException $e) {
            $this->error('Error fetching AI-generated content: '.$e->getMessage());
        }

        return 0;
    }

    private function createAiPrompt(string $name, string $model, array $schema): string
    {
        $prompt = "Generate a Laravel factory named '{$name}' for the '{$model}' model.";
        $prompt .= "\nThe current schema of the table is as follows:\n".implode(', ', $schema);
        $prompt .= "\nConsider generating relations too based on the column names.";

        $prompt .= "\nProvide only the final Laravel factory code without any explanations or additional context. (start with <?php)";

        return $prompt;
    }

    /**
     * @throws RequestException
     */
    private function fetchAiGeneratedContent(string $prompt): string
    {
        return (new OpenAi())->execute($prompt, 2000);
    }

    private function createFactoryFile(string $name, string $content)
    {
        $path = database_path('factories');
        if (! Str::endsWith($name, 'Factory')) {
            $name .= 'Factory';
        }

        $name = "{$name}Factory.php";
        $filepath = "{$path}/{$name}";

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($filepath, $content);

        $this->info(sprintf('Factory [%s] created successfully.', $name));
    }
}
