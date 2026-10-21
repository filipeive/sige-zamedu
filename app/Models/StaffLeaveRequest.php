<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\BelongsToSchool;

class StaffLeaveRequest extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'staff_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('leave_requests')
            ->logOnly([
                'staff_id',
                'leave_type',
                'start_date',
                'end_date',
                'reason',
                'status',
                'rejection_reason',
                'approved_by',
                'approved_at',
            ])
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function staff()
    {
        return $this->belongsTo(Teacher::class, 'staff_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getDaysRequestedAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getLeaveTypeNameAttribute()
    {
        $types = [
            'sick' => 'Licença Médica',
            'vacation' => 'Férias',
            'personal' => 'Assunto Pessoal',
            'maternity' => 'Licença Maternidade',
            'other' => 'Outro'
        ];

        return $types[$this->leave_type] ?? 'Não definido';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Aprovada',
            'rejected' => 'Rejeitada',
            'pending' => 'Pendente',
            default => ucfirst((string) $this->status)
        };
    }
}
