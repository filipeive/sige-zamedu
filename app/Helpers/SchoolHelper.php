<?php

use App\Models\School;
use App\Models\Setting;
use App\Services\SchoolContext;

/**
 * Obter a escola do contexto corrente do utilizador (suporte, contexto explícito ou utilizador autenticado).
 */
if (!function_exists('current_school')) {
    function current_school(): ?School
    {
        $schoolId = SchoolContext::getSchoolId()
            ?? session('support_mode_school_id')
            ?? auth()->user()?->school_id;

        if ($schoolId) {
            return School::find($schoolId);
        }

        return null;
    }
}

/**
 * Obter o nome dinâmico da escola do contexto multi-tenant.
 */
if (!function_exists('current_school_name')) {
    function current_school_name(string $default = 'ZamEdu'): string
    {
        $school = current_school();
        if ($school && !empty($school->name)) {
            return $school->name;
        }

        return Setting::get('school_name', $default);
    }
}

/**
 * Verifica se a escola registada é pública.
 */
if (!function_exists('is_public_school')) {
    function is_public_school(): bool
    {
        $school = current_school();
        if ($school && !empty($school->type)) {
            return $school->type === 'public';
        }

        return Setting::get('institution_type', 'escola_privada') === 'escola_publica';
    }
}

/**
 * Verifica se a escola registada é privada.
 */
if (!function_exists('is_private_school')) {
    function is_private_school(): bool
    {
        return !is_public_school();
    }
}
