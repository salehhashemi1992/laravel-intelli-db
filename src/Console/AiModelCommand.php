<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class AiModelCommand.
 *
 * A Laravel console command to create a new model using AI.
 */
class AiModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $name = 'ai:model';

    /**
     * The console command description.
     */
    protected $description = 'Create a new model using AI';

    public function __construct(private readonly OpenAi $openAi)
    {
        parent::__construct();
    }

    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputOption::VALUE_REQUIRED, 'The name of the model');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->getNameArgument();

        if (! $this->tableExistsForModel($name)) {
            return 1;
        }

        $schema = $this->getSchemaForModel($name);
        $prompt = $this->createAiPrompt($name, $schema);

        $this->info('Generating AI model, this might take a few moments...');

        try {
            $modelContent = $this->fetchAiGeneratedContent($prompt);
            $this->createModelFile($name, $modelContent);
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
     * Check if the table exists for the provided model.
     *
     * @return bool true if the table exists, false otherwise
     */
    private function tableExistsForModel(string $name): bool
    {
        $table = Str::snake(Str::pluralStudly(class_basename($name)));

        if (! Schema::hasTable($table)) {
            $this->error("The table for the provided model '{$name}' does not exist.");

            return false;
        }

        return true;
    }

    /**
     * Get the schema for the provided model.
     *
     * @return string[] The schema for the model (list of column names).
     */
    private function getSchemaForModel(string $name): array
    {
        $table = Str::snake(Str::pluralStudly(class_basename($name)));

        return Schema::getColumnListing($table);
    }

    /**
     * Create an AI prompt using the provided information.
     *
     * @param  string[]  $schema The schema information, if available.
     */
    private function createAiPrompt(string $name, array $schema): string
    {
        $prompt = "Generate a Laravel model named '{$name}' with PHP DocBlock comments for properties, relationships, and methods.";
        $prompt .= "\nThe current schema of the table is as follows:\n".implode(', ', $schema);
        $prompt .= "\nConsider generating relationships, mutators, and accessors based on the model's columns and their names.";

        $prompt .= "\nInclude type hints for methods and their arguments.";
        $prompt .= "\nThe PHP DocBlock comments should include information about properties, their types, relationships, and methods.";

        $prompt .= "\nProvide only the final Laravel model code without any explanations or additional context. (start with <?php)";

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
        return $this->openAi->execute($prompt, 3500);
    }

    /**
     * Create a model file using the provided name and content.
     *
     * @param  string  $name  The model name
     * @param  string  $content  The model content
     */
    private function createModelFile(string $name, string $content): void
    {
        $path = app_path('Models');
        $name = "{$name}.php";
        $filepath = "{$path}/{$name}";

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($filepath, $content);

        $this->info(sprintf('Model [%s] created successfully.', $name));
    }

    /**
     * Prompt for missing arguments.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What should the model be named?',
        ];
    }
}
