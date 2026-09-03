
---

## 📲 5. Integração SMS — httpSMS (CloudEvents 1.0)

O ZamEdu integra-se com o [httpSMS](https://httpsms.com) para envio e recepção de SMS usando o **cartão SIM real** de um dispositivo Android (sem gateways pagos de intermediários).

### 5.1. Variáveis de Ambiente

```ini
# Driver SMS (httpsms | log)
SMS_DRIVER=httpsms
SMS_ENABLED=true
SMS_SIMULATE=false

# Chave API httpSMS (obtida em https://httpsms.com/settings)
HTTPSMS_KEY=uk_xxxxxxxxxxxxxxxxxxxxxxxx
HTTPSMS_FROM=+258862134230   # Número E.164 do SIM no Android app
```

### 5.2. Envio de SMS (Outbound)

Endpoint chamado pelo `HttpSmsDriver`:

```
POST https://httpsms.com/v1/messages/send
Headers:
  x-api-key: <HTTPSMS_KEY>
  Content-Type: application/json
Body:
  {
    "from": "+258862134230",
    "to":   "+258847240296",
    "text": "Exmos pais, o seu educando..."
  }
```

**Resposta (201 Created):**
```json
{
  "data": {
    "id":      "4606eb17-21bd-4859-85c8-956802c0c7a9",
    "owner":   "+258862134230",
    "contact": "+258847240296",
    "content": "Exmos pais...",
    "status":  "Pending"
  }
}
```

O `provider_id` (UUID) é gravado em `sms_logs.provider_id` para correlacionar com os webhooks de entrega.

### 5.3. Webhook de Eventos CloudEvents 1.0

**URL para configurar em https://httpsms.com/webhooks:**
```
https://SEU-DOMINIO/webhooks/httpsms
```

**Eventos subscritos:**

| Tipo | Acção |
|---|---|
| `message.phone.received` | Guarda em `sms_inbox` + auto-match aluno/encarregado + notifica staff via in-app |
| `message.phone.delivered` | Actualiza `sms_logs.status` para `delivered` + `delivered_at` |
| `message.phone.sent` | Marca o log como enviado (idempotente) |
| `message.send.failed` | Marca o log como `failed` com `error` |
| `message.send.expired` | Marca o log como `failed` (motivo: expirado) |
| `phone.heartbeat.online` | Log info (telefone está online) |
| `phone.heartbeat.offline` | Log warning + alerta |

**Exemplo de payload `message.phone.received`:**
```json
{
  "specversion": "1.0",
  "type": "message.phone.received",
  "source": "/httpsms.com/messages",
  "id": "evt_abc123",
  "time": "2026-09-03T23:00:00Z",
  "datacontenttype": "application/json",
  "data": {
    "message_id": "msg_def456",
    "owner":      "+258862134230",
    "contact":    "+258847240296",
    "content":    "Bom dia, o meu filho não vai à escola hoje.",
    "sim":        "SIM1",
    "timestamp":  "2026-09-03T23:00:00Z"
  }
}
```

**Auto-match por telefone (E.164):**
1. Procura o telefone na tabela `parents.phone`.
2. Se não encontrar, procura em `students.emergency_phone`.
3. Se encontrar, vincula `sms_inbox.student_id` e `sms_inbox.parent_id`.
4. Notifica todos os utilizadores com permissão `view_students` via `GenericNotification` (database channel).

### 5.4. Arquitectura do Motor SMS

```
SmsNotificationService::send(phone, message, channel, context, student)
  │
  ├─→ SmsManager::driver() (factory)
  │     ├─→ HttpSmsDriver  (POST /v1/messages/send, header x-api-key)
  │     └─→ LogSmsDriver   (driver de simulação para dev)
  │
  └─→ SmsLog::create([...])  (auditoria sempre, mesmo em modo simulação)
```

O `SmsChannel` permite enviar SMS via `Notifiable::route('sms', '+258...')` e é registado em `AppServiceProvider::boot()`:

```php
Notification::extend('sms', function ($app) {
    return new SmsChannel($app->make(SmsNotificationService::class));
});
```

### 5.5. Teste Manual do Webhook (curl)

```bash
curl -X POST https://SEU-DOMINIO/webhooks/httpsms \
  -H "Content-Type: application/json" \
  -d '{
    "type": "message.phone.received",
    "data": {
      "message_id": "test_001",
      "owner": "+258862134230",
      "contact": "+258847240296",
      "content": "Teste de mensagem recebida",
      "sim": "SIM1"
    }
  }'
```

Resposta esperada: `200 OK` com `{"status":"ok"}`.

---
