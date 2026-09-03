<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpSmsDriver implements SmsDriverInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $from,
        protected string $baseUrl = 'https://api.httpsms.com',
        protected bool $simulate = false,
    ) {}

    public function send(string $to, string $message): array
    {
        if (empty($this->apiKey)) {
            Log::warning('HttpSMS driver sem API key', ['to' => $to]);
            return ['success' => false, 'error' => 'API key em falta (defina HTTPSMS_KEY no .env)'];
        }

        // Modo simulação: útil em desenvolvimento quando a chave não tem saldo/conta activa
        if ($this->simulate) {
            Log::info('HttpSMS [SIMULATE]', [
                'from' => $this->from,
                'to' => $to,
                'message' => $message,
            ]);
            return [
                'success' => true,
                'provider_id' => 'simulated-'.uniqid(),
                'simulated' => true,
            ];
        }

        $payload = [
            'from' => $this->from,
            'to' => $to,
            'content' => $message,
        ];

        try {
            $response = Http::withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->asJson()
                ->post(rtrim($this->baseUrl, '/').'/v1/messages/send', $payload);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'provider_id' => $body['data']['id']
                        ?? $body['id']
                        ?? $body['message_id']
                        ?? null,
                ];
            }

            $status = $response->status();
            $bodyText = $response->body();
            $detail = $this->interpretError($status, $bodyText);

            Log::error('HttpSMS erro', [
                'to' => $to,
                'status' => $status,
                'body' => $bodyText,
            ]);

            return [
                'success' => false,
                'error' => $detail,
                'http_status' => $status,
            ];
        } catch (\Throwable $e) {
            Log::error('HttpSMS excepção', ['to' => $to, 'msg' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Traduz o erro HTTP do httpSMS para uma mensagem útil para o utilizador.
     */
    protected function interpretError(int $status, string $body): string
    {
        $bodyData = json_decode($body, true) ?: [];
        $apiMessage = $bodyData['message'] ?? $bodyData['error'] ?? null;

        return match ($status) {
            401 => 'HttpSMS: chave API inválida ou expirada (HTTP 401). '
                  .'Verifique HTTPSMS_KEY no .env e confirme a conta em https://httpsms.com. '
                  .'Como alternativa, defina SMS_SIMULATE=true para testar sem enviar SMS real.',
            403 => 'HttpSMS: acesso negado (HTTP 403). '.$apiMessage,
            404 => 'HttpSMS: endpoint não encontrado (HTTP 404). Confirme HTTPSMS_BASE_URL.',
            422 => 'HttpSMS: dados inválidos (HTTP 422). '.$apiMessage,
            429 => 'HttpSMS: limite de requisições atingido (HTTP 429). Aguarde alguns minutos.',
            500, 502, 503, 504 => 'HttpSMS: servidor indisponível (HTTP '.$status.'). Tente novamente.',
            default => 'HttpSMS respondeu '.$status.($apiMessage ? ': '.$apiMessage : ''),
        };
    }
}