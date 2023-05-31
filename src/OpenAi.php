<?php

namespace Salehhashemi\LaravelIntelliDb;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class OpenAi
{
    /**
     * Execute the OpenAI API call with a given prompt.
     *
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function execute(string $prompt, int $maxTokens = 300): string
    {
        $apiKey = config('intelli-db.openAiApiKey');

        if ($apiKey === null) {
            throw new InvalidArgumentException('OpenAI API key is not provided in the configuration file.');
        }

        $input_data = [
            'temperature' => 0.7,
            'max_tokens' => $maxTokens,
            'frequency_penalty' => 0,
            'model' => config('intelli-db.model'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(90)
            ->post('https://api.openai.com/v1/chat/completions', $input_data);

        if ($response->successful()) {
            $complete = $response->json();

            return $complete['choices'][0]['message']['content'];
        } else {
            throw new RequestException($response);
        }
    }
}
