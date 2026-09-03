<?php

namespace App\Notifications\Channels;

use App\Models\ParentModel;
use App\Models\User;
use App\Services\Sms\SmsManager;
use Illuminate\Notifications\Messages\Message;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function __construct(protected SmsManager $sms) {}

    /**
     * Enviar a notificação por SMS.
     *
     * Suporta:
     *   - User (com campo phone) — lê de User
     *   - ParentModel — lê phone
     *   - Student — tenta telefone do encarregado
     *   - Qualquer objecto com método routeNotificationForSms() / phone
     */
    public function send(object $notifiable, Message $notification): void
    {
        if (!($notification instanceof SmsMessage)) {
            return;
        }

        $phone = $this->resolvePhone($notifiable);

        if (empty($phone)) {
            Log::warning('SMS ignorado — destinatário sem telefone', [
                'class' => get_class($notifiable),
                'id' => $notifiable->id ?? null,
            ]);
            return;
        }

        $result = $this->sms->send($phone, $notification->content());

        Log::info('SMS enviado', [
            'to' => $phone,
            'success' => $result['success'] ?? false,
            'provider_id' => $result['provider_id'] ?? null,
            'error' => $result['error'] ?? null,
        ]);
    }

    protected function resolvePhone(object $notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForSms')) {
            $phone = $notifiable->routeNotificationForSms();
            if ($phone) return $phone;
        }

        if ($notifiable instanceof User) {
            return $notifiable->phone ?? null;
        }

        if ($notifiable instanceof ParentModel) {
            return $notifiable->phone ?? null;
        }

        if (method_exists($notifiable, 'phone')) {
            return $notifiable->phone ?? null;
        }

        return null;
    }
}