<?php

namespace App\Services\Sms;

use App\Models\SmsLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    public function __construct(protected SmsManager $sms) {}

    /**
     * Enviar um SMS e gravar automaticamente no histórico (audit log).
     *
     * @param  string       $phone       E.164 ou número local (normalizado pelo manager)
     * @param  string       $message
     * @param  string       $channel     guardian|event|payment|bulk|system
     * @param  string|null  $context     slug semântico (mark-exit, falta, propina, ...)
     * @param  Student|null $student
     * @param  User|null    $user        utilizador que despoletou (auth)
     * @param  string|null  $recipientName
     */
    public function send(
        string $phone,
        string $message,
        string $channel = 'guardian',
        ?string $context = null,
        ?Student $student = null,
        ?User $user = null,
        ?string $recipientName = null,
    ): array {
        // Normaliza o telefone uma única vez (E.164) para o log E para envio
        $manager = app(SmsManager::class);
        $phoneNormalized = $manager->normalizePhone($phone) ?? $phone;
        $isSimulated = (bool) config('services.sms.simulate', false);

        $log = SmsLog::create([
            'user_id' => $user?->id ?? auth()->id(),
            'student_id' => $student?->id,
            'channel' => $channel,
            'context' => $context,
            'recipient_name' => $recipientName ?? $student?->parent?->full_name ?? $student?->full_name,
            'phone' => $phoneNormalized, // sempre formato E.164 no log
            'message' => $message,
            'status' => 'pending',
            'provider' => config('services.sms.driver', 'httpsms'),
        ]);

        try {
            $result = $manager->send($phone, $message);

            $log->update([
                'status' => ($result['success'] ?? false)
                    ? ($isSimulated ? 'disabled' : 'sent')
                    : 'failed',
                'provider_id' => $result['provider_id'] ?? null,
                'provider_response' => is_array($result) ? json_encode($result, JSON_UNESCAPED_UNICODE) : null,
                'error' => $result['error'] ?? null,
                'sent_at' => ($result['success'] ?? false) ? now() : null,
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::error('SmsNotificationService erro', ['err' => $e->getMessage()]);
            $log->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Disparar SMS em massa a vários destinatários (cada um com mensagem personalizada).
     *
     * @param  array<int, array{phone:string, message:string, name?:string, student_id?:int}>  $recipients
     */
    public function sendBulk(array $recipients, string $context = 'bulk-campaign', ?User $user = null): array
    {
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $r) {
            $student = !empty($r['student_id']) ? Student::find($r['student_id']) : null;
            $result = $this->send(
                phone: $r['phone'],
                message: $r['message'],
                channel: 'bulk',
                context: $context,
                student: $student,
                user: $user,
                recipientName: $r['name'] ?? null,
            );

            if ($result['success'] ?? false) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'total' => count($recipients)];
    }
}