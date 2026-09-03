<?php

namespace App\Services\Ai;

interface AiDriverInterface
{
    /**
     * Executa um prompt de chat completion.
     *
     * @param  array<int, array{role:string, content:string}>  $messages
     * @param  array<string, mixed>  $options  (temperature, max_tokens, etc.)
     * @return array{success: bool, content?: string, error?: string, usage?: array}
     */
    public function chat(array $messages, array $options = []): array;
}