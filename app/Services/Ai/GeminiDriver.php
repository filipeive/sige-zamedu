<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiDriver implements AiDriverInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta',
    ) {}

    public function chat(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'Gemini API key em falta'];
        }

        $model = $options['model'] ?? $this->model;
        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $msg) {
            if (($msg['role'] ?? '') === 'system') {
                $systemInstruction = $msg['content'];
                continue;
            }
            $contents[] = [
                'role' => ($msg['role'] ?? 'user') === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content'] ?? '']],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['max_tokens'] ?? 1024,
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        try {
            $url = rtrim($this->baseUrl, '/').'/models/'.urlencode($model).':generateContent?key='.$this->apiKey;

            $response = Http::acceptJson()->asJson()->timeout(60)->post($url, $payload);

            if ($response->successful()) {
                $body = $response->json();
                $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return [
                    'success' => true,
                    'content' => $text,
                    'usage' => $body['usageMetadata'] ?? null,
                    'model' => $model,
                ];
            }

            Log::error('Gemini erro', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => 'Gemini HTTP '.$response->status()];
        } catch (\Throwable $e) {
            Log::error('Gemini excepção', ['msg' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}