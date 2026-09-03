<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsInbox extends Model
{
    use HasFactory;

    protected $table = 'sms_inbox';

    protected $fillable = [
        'provider_message_id',
        'direction',
        'from_phone',
        'from_name',
        'to_phone',
        'content',
        'sim',
        'student_id',
        'parent_id',
        'matched_user_id',
        'is_read',
        'read_at',
        'read_by',
        'notified',
        'received_at',
        'raw_payload',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'notified' => 'boolean',
        'read_at' => 'datetime',
        'received_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id', 'user_id');
    }

    public function matchedUser()
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function getPreviewAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->content, 80);
    }
}