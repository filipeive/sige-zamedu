<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\SmsInbox;
use App\Models\Student;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HttpSmsWebhookController extends Controller
{
    public function __construct(protected SmsManager $sms) {}

    /**
     * Webhook httpSMS — CloudEvents 1.0 spec.
     *
     * Eventos suportados:
     *  - message.phone.received   → guarda em sms_inbox (resposta de encarregado/aluno)
     *  - message.phone.sent       → actualiza sms_logs.status para "delivered"
     *  - message.phone.delivered  → actualiza sms_logs.status para "delivered"
     *  - message.send.failed      → actualiza sms_logs.status para "failed" + erro
     *  - message.send.expired     → actualiza sms_logs.status para "failed" + motivo
     *  - phone.heartbeat.offline  → log + alerta
     *  - phone.heartbeat.online   → log
     */
    public function handle(Request $request)
    {
        $payload = $request->json()->all();
        $type = $payload['type'] ?? '';
        $data = $payload['data'] ?? [];

        Log::info('httpSMS webhook', ['type' => $type, 'data_keys' => array_keys($data)]);

        try {
            match ($type) {
                'message.phone.received' => $this->handleReceived($data, $payload),
                'message.phone.delivered', 'message.phone.sent' => $this->handleDelivered($data),
                'message.send.failed', 'message.send.expired' => $this->handleFailed($data, $type),
                'phone.heartbeat.offline' => $this->handlePhoneOffline($data),
                'phone.heartbeat.online' => $this->handlePhoneOnline($data),
                default => Log::info('httpSMS webhook ignorado', ['type' => $type]),
            };
        } catch (\Throwable $e) {
            Log::error('httpSMS webhook erro', ['err' => $e->getMessage(), 'type' => $type]);
        }

        // CloudEvents espera 200 OK (ou 202 Accepted)
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * SMS recebido do encarregado/aluno → guardar em sms_inbox + tentar match + notificar.
     */
    protected function handleReceived(array $data, array $raw): void
    {
        $fromPhone = $this->normalizePhone($data['contact'] ?? '');
        $content = (string) ($data['content'] ?? '');
        $messageId = $data['message_id'] ?? $data['id'] ?? null;

        if (empty($fromPhone) || $content === '') {
            return;
        }

        // Evitar duplicados
        if ($messageId && SmsInbox::where('provider_message_id', $messageId)->exists()) {
            return;
        }

        $match = $this->matchContact($fromPhone);

        $inbox = SmsInbox::create([
            'provider_message_id' => $messageId,
            'direction' => 'incoming',
            'from_phone' => $fromPhone,
            'from_name' => $match['name'] ?? null,
            'to_phone' => $this->normalizePhone($data['owner'] ?? ''),
            'content' => $content,
            'sim' => $data['sim'] ?? null,
            'student_id' => $match['student_id'] ?? null,
            'parent_id' => $match['parent_id'] ?? null,
            'matched_user_id' => $match['user_id'] ?? null,
            'received_at' => isset($data['timestamp']) ? \Carbon\Carbon::parse($data['timestamp']) : now(),
            'raw_payload' => $raw,
        ]);

        $this->dispatchNotification($inbox);
    }

    /**
     * Entrega confirmada → marcar log como delivered.
     */
    protected function handleDelivered(array $data): void
    {
        $messageId = $data['id'] ?? null;
        if (! $messageId) {
            return;
        }

        \App\Models\SmsLog::where('provider_id', $messageId)
            ->where('status', 'sent')
            ->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'provider_response' => json_encode(['event' => 'delivered', 'at' => now()->toIso8601String()]),
            ]);
    }

    /**
     * Falha no envio.
     */
    protected function handleFailed(array $data, string $type): void
    {
        $messageId = $data['id'] ?? $data['message_id'] ?? null;
        $reason = $data['error_message'] ?? $data['failure_reason'] ?? $type;

        if ($messageId) {
            \App\Models\SmsLog::where('provider_id', $messageId)
                ->update([
                    'status' => 'failed',
                    'error' => $reason,
                    'failed_at' => now(),
                ]);
        }
    }

    protected function handlePhoneOffline(array $data): void
    {
        Log::warning('httpSMS phone OFFLINE', $data);
    }

    protected function handlePhoneOnline(array $data): void
    {
        Log::info('httpSMS phone online', $data);
    }

    /**
     * Tentar identificar aluno/encarregado pelo telefone.
     *
     * @return array{name?:string, student_id?:int, parent_id?:int, user_id?:int}
     */
    protected function matchContact(string $phone): array
    {
        $phone = $this->normalizePhone($phone);
        if (empty($phone)) {
            return [];
        }

        // Gerar variantes para pesquisa (com/sem 258, com 84 vs 86 vs 87...)
        $variants = $this->phoneVariants($phone);

        // 1) Encarregado com este telefone
        $parent = ParentModel::where(function ($q) use ($variants) {
            foreach ($variants as $v) {
                $q->orWhere('phone', 'like', "%{$v}%");
            }
        })->first();

        if ($parent) {
            $student = Student::where('parent_id', $parent->user_id)->first();
            return [
                'name' => $parent->user?->name,
                'parent_id' => $parent->user_id,
                'student_id' => $student?->id,
                'user_id' => $parent->user_id,
            ];
        }

        // 2) Telefone de emergência do aluno
        $student = Student::where(function ($q) use ($variants) {
            foreach ($variants as $v) {
                $q->orWhere('emergency_phone', 'like', "%{$v}%");
            }
        })->first();

        if ($student) {
            return [
                'name' => $student->full_name,
                'student_id' => $student->id,
            ];
        }

        return [];
    }

    /**
     * Gerar variantes do telefone para pesquisa LIKE.
     */
    protected function phoneVariants(string $phone): array
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $variants = [$digits];

        if (str_starts_with($digits, '258')) {
            $variants[] = substr($digits, 3); // remove 258
        } else {
            $variants[] = '258'.$digits;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    protected function normalizePhone(?string $phone): ?string
    {
        return $this->sms->normalizePhone($phone);
    }

    /**
     * Notificar staff relevante (admin/secretaria +班主任 do aluno) via Notification in-app.
     */
    protected function dispatchNotification(SmsInbox $inbox): void
    {
        $studentName = $inbox->student?->full_name ?? $inbox->from_name ?? 'Desconhecido';
        $preview = Str::limit($inbox->content, 60);

        $notif = [
            'title' => "📩 SMS de {$studentName}",
            'message' => $preview,
            'action_url' => route('sms.inbox.show', $inbox),
            'type' => 'sms_inbox',
            'inbox_id' => $inbox->id,
            'from_phone' => $inbox->from_phone,
        ];

        // Notifica todos os utilizadores com permissão de ver students
        $recipients = \App\Models\User::permission('view_students')->get();
        foreach ($recipients as $user) {
            $user->notify(new \App\Notifications\GenericNotification($notif));
        }

        $inbox->update(['notified' => true]);
    }
}