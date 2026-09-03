<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsDriver implements SmsDriverInterface
{
    public function send(string $to, string $message): array
    {
        Log::channel(config('logging.default'))->info('[SMS:LOG]', [
            'to' => $to,
            'message' => $message,
        ]);

        return ['success' => true, 'provider_id' => 'log-'.uniqid()];
    }
}