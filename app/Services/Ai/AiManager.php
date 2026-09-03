<?php

namespace App\Services\Ai;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class AiManager
{
    public function __construct(protected Container $app) {}

    public function driver(?string $driver = null): AiDriverInterface
    {
        $driver = $driver ?: config('services.ai.driver', 'openrouter');
        $config = config('services.ai');

        return match ($driver) {
            'openrouter' => new OpenRouterDriver(
                apiKey: $config['openrouter']['api_key'] ?? '',
                model: $config['openrouter']['model'] ?? 'google/gemini-2.5-flash',
                baseUrl: $config['openrouter']['base_url'] ?? 'https://openrouter.ai/api/v1',
            ),
            'gemini' => new GeminiDriver(
                apiKey: $config['gemini']['api_key'] ?? '',
                model: $config['gemini']['model'] ?? 'google/gemini-2.5-flash',
                baseUrl: $config['gemini']['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta',
            ),
            default => throw new InvalidArgumentException("Driver AI [{$driver}] não suportado."),
        };
    }

    /**
     * Pede uma resposta ao modelo. Respeita services.ai.enabled.
     */
    public function ask(array $messages, array $options = []): array
    {
        if (!config('services.ai.enabled', false)) {
            return [
                'success' => true,
                'content' => '[AI desativado] Esta funcionalidade está desligada. Defina AI_ENABLED=true no .env para activar o assistente.',
            ];
        }

        return $this->driver()->chat($messages, $options);
    }
}