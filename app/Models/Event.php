<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Event extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'target_audience',
        'type',
        'created_by',
        'send_notification',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'send_notification' => 'boolean',
    ];

    // Relacionamentos
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', today());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('event_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('event_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
    //createdby
    
}