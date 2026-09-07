<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Conversation extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'title',
        'driver',
        'model',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }
}