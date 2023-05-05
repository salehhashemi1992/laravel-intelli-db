<?php

namespace Salehhashemi\LaravelIntelliDb;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class OpenAi
{
    /**
     * Execute the OpenAI API call with a given prompt.
     *
     * @throws RequestException
     */
    public static function execute(string $prompt, int $maxTokens = 300): array
    {
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

        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer '.config('intelli-db.openAiApiKey'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', $input_data);

        if ($response->successful()) {
            $complete = $response->json();

            return $complete['choices'][0]['message']['content'];
        } else {
            throw new RequestException($response);
        }
    }
}
