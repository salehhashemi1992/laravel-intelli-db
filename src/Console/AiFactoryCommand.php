<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class AiFactoryCommand.
 *
 * A Laravel console command to create a new factory using AI.
 */
class AiFactoryCommand extends Command
{
    use ModelHelperTrait;

    /**
     * The name and signature of the console command.
     */
    protected $name = 'ai:factory';

    /**
     * The console command description.
     */
    protected $description = 'Create a new factory using AI';

    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->addArgument('name', InputOption::VALUE_REQUIRED, 'The name of the factory')
            ->addOption('model', 'm', InputOption::VALUE_REQUIRED, 'The model for the factory');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->getNameArgument();
        $model = $this->getModelOption();

        try {
            $model = $this->qualifyModel($model);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        if (! $this->tableExistsForModel($model)) {
            return 1;
        }

        $schema = $this->getSchemaForModel($model);
        $prompt = $this->createAiPrompt($name, $model, $schema);

        try {
            $factoryContent = $this->fetchAiGeneratedContent($prompt);
            $this->createFactoryFile($name, $factoryContent);
        } catch (RequestException $e) {
            $this->error('Error fetching AI-generated content: '.$e->getMessage());
        }

        return 0;
    }

    /**
     * Get the 'name' argument or prompt the user if it's not provided.
     */
    private function getNameArgument(): string
    {
        $name = $this->argument('name');

        if (! $name) {
            $name = $this->ask($this->promptForMissingArgumentsUsing()['name']);
        }

        return $name;
    }

    /**
     * Get the 'model' option or prompt the user if it's not provided.
     */
    private function getModelOption(): string
    {
        $model = $this->option('model');

        if (! $model) {
            $model = $this->ask('Please provide the model for the factory');
        }

        return $model;
    }

    /**
     * Check if the table exists for the provided model.
     *
     * @return bool true if the table exists, false otherwise
     */
    private function tableExistsForModel(string $model): bool
    {
        $table = (new $model)->getTable();

        if (! Schema::hasTable($table)) {
            $this->error("The table for the provided model '{$model}' does not exist.");

            return false;
        }

        return true;
    }

    /**
     * Get the schema for the provided model.
     *
     * @return array The schema for the model
     */
    private function getSchemaForModel(string $model): array
    {
        $table = (new $model)->getTable();

        return Schema::getColumnListing($table);
    }

    /**
     * Create an AI prompt using the provided information.
     */
    private function createAiPrompt(string $name, string $model, array $schema): string
    {
        $prompt = "Generate a Laravel factory named '{$name}' for the '{$model}' model.";
        $prompt .= "\nThe current schema of the table is as follows:\n".implode(', ', $schema);
        $prompt .= "\nConsider generating relations too, based on the column names (like user_id) using appropriate methods such as afterCreating, etc.";
        $prompt .= "\nProvide only the final Laravel factory code without any explanations or additional context. (start with <?php)";
        $prompt .= "\nInclude type hints for methods and their arguments.";

        return $prompt;
    }

    /**
     * Fetch AI-generated content using the provided prompt.
     *
     * @param  string  $prompt  The AI prompt
     * @return string The AI-generated content
     *
     * @throws RequestException
     */
    private function fetchAiGeneratedContent(string $prompt): string
    {
        return (new OpenAi())->execute($prompt, 2000);
    }

    /**
     * Create a factory file using the provided name and content.
     *
     * @param  string  $name  The factory name
     * @param  string  $content  The factory content
     */
    private function createFactoryFile(string $name, string $content)
    {
        $path = database_path('factories');
        if (! Str::endsWith($name, 'Factory')) {
            $name .= 'Factory';
        }

        $name = "{$name}.php";
        $filepath = "{$path}/{$name}";

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($filepath, $content);

        $this->info(sprintf('Factory [%s] created successfully.', $name));
    }

    /**
     * Prompt for missing arguments.
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What should the factory be named?',
        ];
    }
}
