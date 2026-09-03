<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $table = 'sms_logs';

    protected $fillable = [
        'user_id',
        'student_id',
        'channel',
        'context',
        'recipient_name',
        'phone',
        'message',
        'status',
        'provider',
        'provider_id',
        'provider_response',
        'error',
        'cost',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'cost' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'sent' => ['class' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'label' => 'Enviado', 'icon' => 'fa-check'],
            'failed' => ['class' => 'bg-rose-100 text-rose-700 border-rose-200', 'label' => 'Falhou', 'icon' => 'fa-circle-xmark'],
            'disabled' => ['class' => 'bg-amber-100 text-amber-700 border-amber-200', 'label' => 'Desactivado', 'icon' => 'fa-pause'],
            default => ['class' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'Pendente', 'icon' => 'fa-clock'],
        };
    }

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'guardian' => 'Encarregado',
            'event' => 'Evento',
            'payment' => 'Pagamento',
            'bulk' => 'Disparo em massa',
            'system' => 'Sistema',
            default => ucfirst($this->channel ?? '—'),
        };
    }
}