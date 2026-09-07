<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Traits\BelongsToSchool;

class Communication extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'message',
        'target_audience',
        'priority',
        'attachments',
        'created_by',
        'is_published',
        'is_public',
        'publish_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'publish_at' => 'datetime',
        'is_published' => 'boolean',
        'is_public' => 'boolean',
    ];

    // Relacionamentos
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
    }

    public function scopeForTeachers($query)
    {
        return $query->where(function ($q) {
            $q->where('target_audience', 'teachers')
                ->orWhere('target_audience', 'all');
        });
    }

    public function scopeForParents($query)
    {
        return $query->where(function ($q) {
            $q->where('target_audience', 'parents')
                ->orWhere('target_audience', 'all');
        });
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getExcerptAttribute()
    {
        return Str::limit($this->message, 100);
    }

    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger'
        ][$this->priority] ?? 'secondary';
    }

    public function getTargetAudienceNameAttribute()
    {
        return [
            'all' => 'Todos',
            'teachers' => 'Professores',
            'students' => 'Alunos',
            'parents' => 'Encarregados'
        ][$this->target_audience] ?? 'Não definido';
    }
}