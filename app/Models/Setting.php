<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\BelongsToSchool;
use App\Services\SchoolContext;

class Setting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = ['school_id', 'key', 'value', 'group'];

    protected static function booted()
    {
        static::creating(function ($setting) {
            if (!$setting->school_id) {
                $setting->school_id = SchoolContext::getSchoolId() 
                    ?? auth()->user()?->school_id 
                    ?? \App\Models\School::value('id') 
                    ?? 1;
            }
        });
    }

    /**
     * Define ou atualiza uma configuração por chave com contexto de escola seguro.
     */
    public static function set(string $key, ?string $value, string $group = 'general', ?int $targetSchoolId = null): self
    {
        $schoolId = $targetSchoolId 
            ?? SchoolContext::getSchoolId() 
            ?? auth()->user()?->school_id 
            ?? \App\Models\School::value('id') 
            ?? 1;

        $setting = static::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->where('key', $key)
            ->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

        if (!$setting) {
            $setting = static::withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->where('key', $key)
                ->first();
        }

        if ($setting) {
            $setting->update([
                'value' => $value,
                'group' => $group,
                'school_id' => $schoolId,
            ]);
            Cache::forget("setting_{$key}");
            return $setting;
        }

        $newSetting = static::create([
            'school_id' => $schoolId,
            'key' => $key,
            'value' => $value,
            'group' => $group,
        ]);

        Cache::forget("setting_{$key}");

        return $newSetting;
    }

    /**
     * Obtém o valor de uma configuração por chave com fallback para o escopo global.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $schoolId = SchoolContext::getSchoolId()
            ?? session('support_mode_school_id')
            ?? auth()->user()?->school_id;

        // Multi-tenant: dados de identidade da escola vêm sempre do registo School (fonte soberana)
        if ($schoolId) {
            $identity = static::resolveSchoolIdentity((int) $schoolId, $key);
            if ($identity !== null && $identity !== '') {
                return $identity;
            }
        }

        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            $setting = static::withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->where('key', $key)
                ->where(function ($q) use ($schoolId) {
                    if ($schoolId) {
                        $q->where('school_id', $schoolId);
                    }
                })
                ->first();
        }

        if (!$setting) {
            $setting = static::withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->where('key', $key)
                ->first();
        }

        return $setting ? $setting->value : $default;
    }

    /**
     * Mapeia chaves de identidade para atributos do modelo School do tenant activo.
     */
    protected static function resolveSchoolIdentity(int $schoolId, string $key): ?string
    {
        static $schools = [];

        if (!array_key_exists($schoolId, $schools)) {
            $schools[$schoolId] = \App\Models\School::withoutGlobalScopes()
                ->with(['province', 'district'])
                ->find($schoolId);
        }

        $school = $schools[$schoolId];
        if (!$school) {
            return null;
        }

        return match ($key) {
            'school_name', 'app_name' => $school->name,
            'school_phone' => $school->phone,
            'school_email' => $school->email,
            'school_address' => $school->address,
            'school_code' => $school->code,
            'director_name' => $school->principal_name,
            'school_province', 'province' => $school->province?->name,
            'school_district', 'district' => $school->district?->name,
            default => null,
        };
    }
}
