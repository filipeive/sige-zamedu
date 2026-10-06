<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSchool;

class PedagogicalMapSubmission extends Model
{
    use HasFactory, BelongsToSchool;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted_zip';
    public const STATUS_APPROVED = 'approved_zip';
    public const STATUS_REJECTED = 'rejected_zip';

    protected $fillable = [
        'school_id',
        'year',
        'term',
        'status',
        'submitted_by',
        'submitted_at',
        'submission_notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'year' => 'integer',
        'term' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted_zip', 'approved_zip']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved_zip';
    }
}
