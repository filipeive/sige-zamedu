<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\BelongsToSchool;

class Payment extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'reference_number',
        'student_id',
        'enrollment_id',
        'type',
        'amount',
        'month',
        'year',
        'due_date',
        'payment_date',
        'status',
        'payment_method',
        'transaction_id',
        'notes',
        'discount',
        'penalty',
        'penalty_percentage',
        'penalty_applied_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'penalty' => 'decimal:2',
        'penalty_percentage' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'penalty_applied_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'payment_date', 'payment_method', 'penalty', 'penalty_percentage'])
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                    ->where('due_date', '<', now());
            });
    }

    public function scopeWithPenalty($query)
    {
        return $query->where('penalty', '>', 0);
    }

    public function scopeBlocked($query)
    {
        return $query->where('due_date', '<', now()->subDays(90));
    }

    // Accessors
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        return $months[$this->month] ?? '';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'paid' => 'Pago',
            'pending' => 'Pendente',
            'overdue' => 'Em Atraso',
            'cancelled' => 'Cancelado',
            default => ucfirst((string) $this->status)
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'matricula' => 'Matrícula',
            'mensalidade' => 'Mensalidade',
            'propina' => 'Propina',
            'exame' => 'Exame',
            'certificado' => 'Certificado',
            'uniforme' => 'Uniforme',
            'transporte' => 'Transporte',
            'alimentacao' => 'Alimentação',
            'outro' => 'Outro',
            default => ucfirst((string) $this->type)
        };
    }

    public function getTotalAmountAttribute()
    {
        $amount = (float) $this->amount;
        $penalty = (float) $this->penalty;
        $discount = (float) $this->discount;

        return $amount + $penalty - $discount;
    }

    public function getOriginalAmountAttribute()
    {
        return (float) $this->amount;
    }

    public function getDaysLateAttribute()
    {
        if ($this->status === 'paid') {
            return 0;
        }

        $dueDate = \Carbon\Carbon::parse($this->due_date);
        $now = now();

        if ($now->isSameDay($dueDate) || $now->isBefore($dueDate)) {
            return 0;
        }

        return $dueDate->diffInDays($now);
    }

    public function getPenaltyStageAttribute()
    {
        $daysLate = $this->days_late;

        if ($daysLate >= 90) {
            return 4;
        } // 100% após 90 dias
        if ($daysLate >= 60) {
            return 3;
        } // 50% após 60 dias
        if ($daysLate >= 30) {
            return 2;
        } // 25% após 30 dias
        if ($daysLate >= 15) {
            return 1;
        } // 10% após 15 dias

        return 0; // Sem multa
    }

    public function getSuggestedPenaltyPercentageAttribute()
    {
        $stage = $this->penalty_stage;

        switch ($stage) {
            case 4: return 100; // 100% após 90 dias
            case 3: return 50;  // 50% após 60 dias
            case 2: return 25;  // 25% após 30 dias
            case 1: return 10;  // 10% após 15 dias
            default: return 0;
        }
    }

    public function getSuggestedPenaltyAmountAttribute()
    {
        $amount = (float) $this->amount;
        $percentage = $this->suggested_penalty_percentage;

        return ($amount * $percentage) / 100;
    }

    public function getIsBlockedAttribute()
    {
        return $this->penalty_stage >= 4; // Bloqueado após 90 dias
    }

    public function getStatusBadgeAttribute()
    {
        $statusColors = [
            'paid' => 'success',
            'pending' => 'warning',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
        ];

        $statusTexts = [
            'paid' => 'Pago',
            'pending' => 'Pendente',
            'overdue' => 'Em Atraso',
            'cancelled' => 'Cancelado',
        ];

        $color = $statusColors[$this->status] ?? 'secondary';
        $text = $statusTexts[$this->status] ?? $this->status;

        $badge = '<span class="badge bg-'.$color.'">'.$text.'</span>';

        if ($this->penalty > 0) {
            $percentage = $this->penalty_percentage ?? 0;
            $badge .= ' <span class="badge bg-danger">Multa: '.$percentage.'%</span>';
        }

        if ($this->is_blocked) {
            $badge .= ' <span class="badge bg-dark">BLOQUEADO</span>';
        }

        return $badge;
    }

    // Métodos
    public static function generateReference($studentId, $month, $year)
    {
        return 'VIS'.str_pad($studentId, 4, '0', STR_PAD_LEFT).
               str_pad($month, 2, '0', STR_PAD_LEFT).
               substr($year, -2);
    }

    /**
     * Verificar se precisa de multa
     */
    public function needsPenaltyApplication()
    {
        return $this->status === 'pending' &&
               $this->due_date < now() &&
               is_null($this->penalty_applied_at) &&
               $this->days_late >= 15;
    }

    /**
     * Aplicar multa automaticamente
     */
    public function applyAutomaticPenalty()
    {
        $daysLate = $this->days_late;
        $penaltyPercentage = 0;

        if ($daysLate >= 90) {
            $penaltyPercentage = 100; // 100% após 90 dias
        } elseif ($daysLate >= 60) {
            $penaltyPercentage = 50;  // 50% após 60 dias
        } elseif ($daysLate >= 30) {
            $penaltyPercentage = 25;  // 25% após 30 dias
        } elseif ($daysLate >= 15) {
            $penaltyPercentage = 10;  // 10% após 15 dias
        }

        if ($penaltyPercentage > 0) {
            $amount = (float) $this->amount;
            $penaltyAmount = ($amount * $penaltyPercentage) / 100;

            $this->update([
                'penalty_percentage' => $penaltyPercentage,
                'penalty' => $penaltyAmount,
                'penalty_applied_at' => now(),
                'status' => 'overdue',
            ]);

            return true;
        }

        return false;
    }
}
