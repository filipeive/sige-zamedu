<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterDriver implements AiDriverInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected string $baseUrl = 'https://openrouter.ai/api/v1',
    ) {}

    public function chat(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'OpenRouter API key em falta'];
        }

        $payload = array_merge([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1024,
        ], $options);

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(60)
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', $payload);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'content' => $body['choices'][0]['message']['content'] ?? '',
                    'usage' => $body['usage'] ?? null,
                    'model' => $body['model'] ?? $this->model,
                ];
            }

            Log::error('OpenRouter erro', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => 'OpenRouter HTTP '.$response->status()];
        } catch (\Throwable $e) {
            Log::error('OpenRouter excepção', ['msg' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}