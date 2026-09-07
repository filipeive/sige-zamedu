<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class GateLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'action',
        'logged_at',
        'method',
        'logged_by',
        'notes',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function loggedBy()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('logged_at', today());
    }

    public function scopeEntries($query)
    {
        return $query->where('action', 'entry');
    }

    public function scopeExits($query)
    {
        return $query->where('action', 'exit');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('logged_at', $date);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isEntry(): bool
    {
        return $this->action === 'entry';
    }

    public function isExit(): bool
    {
        return $this->action === 'exit';
    }

    public function getActionLabelAttribute(): string
    {
        return $this->isEntry() ? 'Entrada' : 'Saída';
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'qr' => 'QR Code',
            'barcode', 'usb' => 'Leitor USB',
            default => 'Manual',
        };
    }
}
