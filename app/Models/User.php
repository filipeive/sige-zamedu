<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToSchool;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status', // REMOVI 'role' daqui
        'phone',
        'avatar',
        'last_login',
        'school_id',
        'province_id',
        'district_id',
        'zip_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    // Adicionar este método para ignorar o campo 'role' se ainda existir na tabela
    public function getRoleAttribute()
    {
        // Retorna o primeiro role do Spatie
        return $this->getRoleNames()->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status']) // REMOVI 'role' daqui
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function secretary()
    {
        return $this->hasOne(Secretary::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function zip()
    {
        return $this->belongsTo(Zip::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Método para compatibilidade (se estiver usando o campo role em algum lugar)
    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('roles', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}