<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    public function chat(string $prompt): string
    {
        try {

            $response = Http::timeout(120)
                ->post('http://127.0.0.1:11434/api/generate', [

                    'model' => 'llama3.1:8b',

                    'prompt' => $prompt,

                    'stream' => false,
                ]);

            if (!$response->successful()) {
                return "HTTP {$response->status()}\n\n".$response->body();
            }

            return $response->json('response');

        } catch (\Throwable $e) {

            return $e->getMessage();

        }
    }
}