<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Exception;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Schema;
use Salehhashemi\LaravelIntelliDb\OpenAi;

class ExtendedMigrationCreator extends MigrationCreator
{
    protected OpenAi $openAi;

    protected bool $useAI;

    protected string $description;

    public function __construct(Filesystem $files, $customStubPath)
    {
        parent::__construct($files, $customStubPath);
        $this->openAi = new OpenAi();
    }

    public function setAiOptions(bool $useAI, string $description)
    {
        $this->useAI = $useAI;
        $this->description = $description;
    }

    /**
     * @throws Exception
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = parent::populateStub($name, $stub, $table);

        if ($this->useAI) {
            $prompt = $this->createPrompt($this->description);

            if (! is_null($table)) {
                $schema = $this->getTableSchema($table);
                $prompt .= "\n\nCurrent schema for the '{$table}' table:\n\n".$schema;
            }

            try {
                $generatedCode = $this->openAi->execute($prompt, 1000);

                // Replace the default content with the AI-generated content
                $stub = str_replace('//', $generatedCode, $stub);
            } catch (RequestException $e) {
                throw new \Exception('Error fetching AI-generated content: '.$e->getMessage());
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $stub;
    }

    private function createPrompt(string $description): string
    {
        return 'Generate the PHP code for a Laravel migration file that does the following: '.
            "\n$description".
            "\n\nProvide only the relevant code to be placed inside the 'up' method of the migration.";
    }

    protected function getTableSchema($table): string
    {
        $columns = Schema::getColumnListing($table);

        return "Table '$table' has the following columns: ".implode(', ', $columns).'.';
    }
}
