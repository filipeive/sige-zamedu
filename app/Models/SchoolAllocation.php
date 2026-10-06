<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year',
        'student_code',
        'student_name',
        'gender',
        'birth_date',
        'origin_school_id',
        'origin_school_name',
        'destination_school_id',
        'grade_level',
        'shift',
        'status',
        'province_id',
        'district_id',
        'notes',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'birth_date' => 'date',
    ];

    /**
     * Escola Secundária de Destino onde a vaga foi alocada.
     */
    public function destinationSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'destination_school_id');
    }

    /**
     * Escola Primária de Origem onde o aluno concluiu a 6ª classe.
     */
    public function originSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'origin_school_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function scopeForYear($query, ?int $year = null)
    {
        $year = $year ?? (function_exists('next_school_year') ? next_school_year() : 2027);
        return $query->where('academic_year', $year);
    }

    public function scopeAllocated($query)
    {
        return $query->where('status', 'allocated');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'allocated' => 'Afetação Oficial Confirmada (Vaga Reservada)',
            'confirmed' => 'Pré-Inscrição Submetida',
            'enrolled' => 'Matrícula Efetivada',
            default => 'Pendente',
        };
    }

    public function getShiftLabelAttribute(): string
    {
        return match (strtolower($this->shift ?? '')) {
            'manha', 'morning' => 'Turno da Manhã',
            'tarde', 'afternoon' => 'Turno da Tarde',
            'noite', 'night' => 'Turno Noturno',
            default => 'Diurno',
        };
    }
}
