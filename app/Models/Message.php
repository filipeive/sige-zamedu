<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Message extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'conversation_id',
        'role',
        'content',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}