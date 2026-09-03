<?php

namespace App\Services\Sms;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class SmsManager
{
    public function __construct(protected Container $app) {}

    public function driver(?string $driver = null): SmsDriverInterface
    {
        $driver = $driver ?: config('services.sms.driver', 'log');
        $config = config('services.sms');

        return match ($driver) {
            'httpsms' => new HttpSmsDriver(
                apiKey: $config['httpsms']['api_key'] ?? env('HTTPSMS_API_KEY', ''),
                from: $config['httpsms']['from'] ?? '',
                baseUrl: $config['httpsms']['base_url'] ?? 'https://api.httpsms.com',
                simulate: (bool) ($config['simulate'] ?? env('SMS_SIMULATE', false)),
            ),
            'log', 'null' => new LogSmsDriver(),
            default => throw new InvalidArgumentException("Driver SMS [{$driver}] não suportado."),
        };
    }

    /**
     * Enviar uma mensagem. Respeita a flag global `services.sms.enabled`.
     */
    public function send(string $to, string $message): array
    {
        $to = $this->normalizePhone($to);

        if (empty($to)) {
            return ['success' => false, 'error' => 'Número de telefone vazio'];
        }

        if (!config('services.sms.enabled', false)) {
            Log::info('[SMS:DISABLED]', ['to' => $to, 'message' => $message]);
            return ['success' => true, 'provider_id' => 'disabled'];
        }

        return $this->driver()->send($to, $message);
    }

    /**
     * Normaliza o número para E.164 (Mozambique +258 por defeito).
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Mozambique (8 ou 9 dígitos começando por 8/2)
        if (preg_match('/^(8|2)\d{7,8}$/', $phone)) {
            return '+258'.$phone;
        }

        if (str_starts_with($phone, '258')) {
            return '+'.$phone;
        }

        return '+'.$phone;
    }
}